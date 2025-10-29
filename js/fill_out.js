document.getElementById('uploadForm').addEventListener('submit', function(e) {
  const fileInput = document.getElementById('profilePic');
  const submitter = document.activeElement;

  // ตรวจสอบว่าเป็นปุ่มอัปโหลดเท่านั้น
  if (submitter && submitter.name === "upload_btn" && !fileInput.value) {
    e.preventDefault();
    alert('กรุณาเลือกรูปภาพก่อนอัปโหลด');
  }
});