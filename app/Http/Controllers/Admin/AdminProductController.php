<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    /**
     * สถานะที่เลือกได้จริงของสินค้า (ตรงกับ enum ในตาราง products)
     *
     * @return array<int, string>
     */
    private const STATUSES = ['available', 'preparing', 'rented', 'inspection', 'not_ready'];

    /** คีย์ session ที่ใช้เก็บข้อมูลระหว่างขั้นตอน "ยืนยันก่อนบันทึก" */
    private const PENDING_SESSION_KEY = 'admin_product_pending';

    public function index(Request $request): View
    {
        // ตัวเลือกการกรองวนหาจากค่าที่ "มีอยู่จริง" ในตาราง products ทั้งหมด
        // (ไม่ใช่แค่จากผลลัพธ์ที่ถูกกรองแล้ว เพื่อไม่ให้ตัวเลือกหายไปเวลากรองซ้อนกัน)
        $categories = $this->distinctCategories();

        $statuses = Product::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $search = trim((string) $request->query('search', ''));
        $categoryFilter = (string) $request->query('category', '');
        $statusFilter = (string) $request->query('status', '');

        $query = Product::query();

        if ($search !== '') {
            $term = '%'.mb_strtolower($search).'%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(product_name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(product_id) LIKE ?', [$term]);
            });
        }

        if ($categoryFilter !== '') {
            $query->where('category', $categoryFilter);
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $products = $query->orderBy('product_name')->get();

        return view('admin.products.index', compact('products', 'categories', 'statuses'));
    }

    /**
     * หน้าเพิ่มสินค้าใหม่ - ใช้เทมเพลตเดียวกับหน้าแก้ไข (admin.products.edit)
     * โดยไม่มี $product ส่งไปให้ (โหมดสร้างใหม่)
     */
    public function create(): View
    {
        return view('admin.products.edit', [
            'product' => null,
            'categories' => $this->distinctCategories(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function edit(string $id): View
    {
        // ดึงข้อมูลเดิมของสินค้าตาม product_id มาเติมในฟอร์มให้ครบ
        $product = Product::findOrFail($id);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->distinctCategories(),
            'statuses' => self::STATUSES,
        ]);
    }

    /**
     * ขั้นตอนที่ 1: รับฟอร์ม (เพิ่มใหม่หรือแก้ไข) -> ตรวจสอบข้อมูล จัดการรูปภาพที่อัปโหลด/ลบ
     * แล้วพักข้อมูลไว้ใน session เพื่อให้แอดมินตรวจทานก่อนกดยืนยันจริงในขั้นตอนถัดไป
     */
    public function confirm(Request $request, ?string $id = null): View
    {
        $product = $id ? Product::findOrFail($id) : null;

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'rental_fee' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'rental_duration_days' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
        ]);

        $existingImages = $product->image ?? [];
        $removeImages = $request->input('remove_images', []);
        $keptImages = array_values(array_diff($existingImages, $removeImages));
        $actuallyRemoved = array_values(array_intersect($existingImages, $removeImages));

        // กำหนด product_id (ถ้าเพิ่มใหม่ สร้างจากตัวอักษรแรกของ Category)
        $productId = $product->product_id ?? $this->generateProductId($validated['category']);

        $newPaths = [];
        $sequence = count($keptImages) + 1;

        foreach ($request->file('images', []) as $file) {
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = $productId.'-'.$sequence.'.'.$extension;

            while (file_exists(public_path('images/'.$filename))) {
                $sequence++;
                $filename = $productId.'-'.$sequence.'.'.$extension;
            }

            $file->move(public_path('images'), $filename);
            $newPaths[] = 'images/'.$filename;
            $sequence++;
        }

        $pending = [
            'mode' => $product ? 'update' : 'create',
            'product_id' => $productId,
            'product_name' => $validated['product_name'],
            'category' => $validated['category'],
            'size' => $validated['size'] ?? null,
            'description' => $validated['description'] ?? null,
            'rental_fee' => $validated['rental_fee'],
            'deposit' => $validated['deposit'],
            'rental_duration_days' => $validated['rental_duration_days'] ?? 1,
            'status' => $validated['status'],
            'images' => array_merge($keptImages, $newPaths),
            'new_uploaded_paths' => $newPaths,
            'removed_images' => $actuallyRemoved,
        ];

        session([self::PENDING_SESSION_KEY => $pending]);

        return view('admin.products.confirm', [
            'pending' => $pending,
            'original' => $product,
        ]);
    }

    /**
     * ขั้นตอนที่ 2: แอดมินกด "ยืนยันบันทึก" ในหน้าตรวจทาน -> ค่อยเขียนข้อมูลจริงลงฐานข้อมูล
     */
    public function commit(Request $request): RedirectResponse
    {
        $pending = session(self::PENDING_SESSION_KEY);

        if (! $pending) {
            return redirect()->route('admin.products.index')
                ->with('error', 'ไม่พบข้อมูลที่รอยืนยัน กรุณากรอกฟอร์มใหม่อีกครั้ง');
        }

        $attributes = [
            'product_name' => $pending['product_name'],
            'category' => $pending['category'],
            'size' => $pending['size'],
            'description' => $pending['description'],
            'rental_fee' => $pending['rental_fee'],
            'deposit' => $pending['deposit'],
            'rental_duration_days' => $pending['rental_duration_days'],
            'status' => $pending['status'],
            'image' => $pending['images'],
        ];

        if ($pending['mode'] === 'update') {
            /** @var Product $product */
            $product = Product::findOrFail($pending['product_id']);
            $product->update($attributes);
            $message = 'บันทึกการแก้ไขสินค้าเรียบร้อยแล้ว';
        } else {
            $attributes['product_id'] = $pending['product_id'];
            Product::create($attributes);
            $message = 'เพิ่มสินค้าใหม่เรียบร้อยแล้ว';
        }

        // ลบไฟล์รูปเดิมที่แอดมินเลือกเอาออก (ลบจริงหลังบันทึกสำเร็จเท่านั้น)
        $this->deletePhysicalImages($pending['removed_images'] ?? []);

        session()->forget(self::PENDING_SESSION_KEY);

        return redirect()->route('admin.products.index')->with('success', $message);
    }

    /**
     * ยกเลิกขั้นตอนยืนยัน - เคลียร์ session และลบไฟล์รูปใหม่ที่เพิ่งอัปโหลดไว้ชั่วคราว (ยังไม่เคยถูกบันทึกจริง)
     */
    public function cancelConfirm(Request $request): RedirectResponse
    {
        $pending = session(self::PENDING_SESSION_KEY);
        session()->forget(self::PENDING_SESSION_KEY);

        if ($pending) {
            $this->deletePhysicalImages($pending['new_uploaded_paths'] ?? []);

            if ($pending['mode'] === 'update') {
                return redirect()->route('admin.products.edit', $pending['product_id']);
            }
        }

        return redirect()->route('admin.products.create');
    }

    /**
     * ลบสินค้าออกจากระบบแบบ soft delete (ผ่าน Eloquent) - ข้อมูลยังอยู่ในฐานข้อมูล
     * แค่ถูกทำเครื่องหมาย deleted_at จึงไม่ปรากฏในรายการอีกต่อไป
     */
    public function destroy(string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'ลบสินค้าออกจากระบบแล้ว');
    }

    /**
     * หมวดหมู่ทั้งหมดที่ "มีอยู่จริง" ในตาราง products ใช้เติม datalist ของฟอร์ม
     *
     * @return Collection<int, string>
     */
    private function distinctCategories()
    {
        return Product::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    /**
     * สร้างรหัสสินค้าใหม่ที่ไม่ซ้ำกับของเดิม (รวมของที่ถูก soft delete ไปแล้วด้วย)
     */
    private function generateProductId(?string $category = null): string
    {
        $next = Product::withTrashed()->count() + 1;
        $candidate = 'P'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);

        while (Product::withTrashed()->where('product_id', $candidate)->exists()) {
            $next++;
            $candidate = 'P'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    /**
     * ลบไฟล์รูปภาพจริงใน public/images อย่างปลอดภัย (จำกัดให้ลบได้เฉพาะในโฟลเดอร์ images เท่านั้น)
     *
     * @param  array<int, string>  $relativePaths  เช่น "images/G001-1.jpeg"
     */
    private function deletePhysicalImages(array $relativePaths): void
    {
        $imagesDir = realpath(public_path('images'));

        if ($imagesDir === false) {
            return;
        }

        foreach ($relativePaths as $relativePath) {
            if (! str_starts_with($relativePath, 'images/')) {
                continue;
            }

            $fullPath = public_path($relativePath);
            $real = realpath($fullPath);

            // ต้องอยู่ภายใต้โฟลเดอร์ images จริง ๆ เท่านั้น กัน path traversal
            if ($real !== false && str_starts_with($real, $imagesDir) && File::exists($real)) {
                File::delete($real);
            }
        }
    }
}
