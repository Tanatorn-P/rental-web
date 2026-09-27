<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Dress - Rental Web</title>
</head>
<body>

   

    <hr>

    <!-- Header Section -->
    <div>
        <small>OCCASION FINDER</small>
        <h1>คุณกำลังหาชุดสำหรับโอกาสอะไร?</h1>
        <p>เริ่มจากโอกาสของคุณ แล้วเราจะช่วยค้นหาชุดที่ว่าง</p>
    </div>

    <!-- Stepper Navigation -->
    <div>
        <ul>
            <li><strong>01 โอกาส</strong></li>
            <li>02 วันที่</li>
            <li>03 เวลา</li>
            <li>04 สไตล์</li>
            <li>05 ขนาด</li>
        </ul>
    </div>

    <!-- Main Filter Form -->
    <form action="#" method="GET">
        
        <!-- Occasion Selection (Category) -->
        <fieldset>
            <legend>เลือกโอกาส (Occasion)</legend>
            
            <label>
                <input type="radio" name="category" value="wedding" checked>
                <span>💍 Wedding (งานแต่งงาน)</span>
            </label>
            <br>
            <label>
                <input type="radio" name="category" value="graduation">
                <span>🎓 Graduation (รับปริญญา)</span>
            </label>
            <br>
            <label>
                <input type="radio" name="category" value="party">
                <span>🎉 Party (งานปาร์ตี้)</span>
            </label>
            <br>
            <label>
                <input type="radio" name="category" value="formal">
                <span>💼 Formal (งานทางการ)</span>
            </label>
            <br>
            <label>
                <input type="radio" name="category" value="photoshoot">
                <span>📷 Photoshoot (ถ่ายภาพ)</span>
            </label>
            <br>
            <label>
                <input type="radio" name="category" value="costume">
                <span>🎭 Costume (งานแฟนซี)</span>
            </label>
        </fieldset>

        <br>

        <!-- Filter Details -->
        <div>
            <div>
                <label for="date">วันที่จัดงาน:</label><br>
                <input type="text" id="date" name="date" value="21 ก.ย. 2569">
            </div>

            <br>

            <div>
                <label for="size">ขนาดที่ต้องการ:</label><br>
                <select id="size" name="size">
                    <option value="M">M</option>
                    <option value="S">S</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>
            </div>

            <br>

            <div>
                <label for="time_slot">ช่วงเวลา:</label><br>
                <select id="time_slot" name="time_slot">
                    <option value="evening">ช่วงเย็น</option>
                    <option value="morning">ช่วงเช้า</option>
                    <option value="all_day">ทั้งวัน</option>
                </select>
            </div>

            <br>

            <div>
                <label for="style">สไตล์:</label><br>
                <select id="style" name="style">
                    <option value="elegant">Elegant</option>
                    <option value="minimal">Minimal</option>
                    <option value="vintage">Vintage</option>
                </select>
            </div>
        </div>

        <br>

        <!-- Submit Button -->
        <button type="submit">🔍 ค้นหาชุดที่ว่าง</button>

    </form>

</body>
</html>
