document.addEventListener("DOMContentLoaded", function() {
    const addBtn = document.getElementById('addActivities');
    const wrapper = document.getElementById('activities-wrapper');
    const requestBtn = document.getElementById('requestCertificate');

    function addRemoveEvent(btn) {
        btn.addEventListener('click', function() {
            btn.closest('.activities-item').remove();
        });
    }

    // เพิ่มกิจกรรม/โปรเจคใหม่
    addBtn.addEventListener('click', function() {
        const div = document.createElement('div');
        div.classList.add('activities-item','mb-4','p-3','border','rounded');
        div.innerHTML = `
            <div class="mb-2">
                <label class="form-label">กิจกรรม</label>
                <input type="text" name="activity[]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">โปรเจค</label>
                <input type="text" name="project[]" class="form-control">
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-item">ลบ</button>
        `;
        wrapper.appendChild(div);
        addRemoveEvent(div.querySelector('.remove-item'));
    });
});
