document.addEventListener("DOMContentLoaded", function() {
    const addBtn = document.getElementById('addExperience');
    const wrapper = document.getElementById('experience-wrapper');

    function addRemoveEvent(btn) {
        btn.addEventListener('click', function() {
            btn.closest('.experience-item').remove();
        });
    }

    // กดเพิ่มประสบการณ์
    addBtn.addEventListener('click', function() {
        const div = document.createElement('div');
        div.classList.add('experience-item','mb-4','p-3','border','rounded');
        div.innerHTML = `
            <div class="mb-2">
                <label class="form-label">ชื่อตำแหน่ง</label>
                <input type="text" name="position[]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">ชื่อบริษัท/องค์กร</label>
                <input type="text" name="company[]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">ระยะเวลาทำงาน</label>
                <input type="text" name="duration[]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">รายละเอียดงานและความสำเร็จ</label>
                <textarea name="description[]" class="form-control" rows="3"></textarea>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-exp">ลบ</button>
        `;
        wrapper.appendChild(div);

        addRemoveEvent(div.querySelector('.remove-exp'));
    });

    // event ลบในรายการที่โหลดมาจาก DB
    document.querySelectorAll('.remove-exp').forEach(addRemoveEvent);
});
