document.addEventListener('DOMContentLoaded', () => {
    const data = USER_CV_DATA; 
    console.log(USER_CV_DATA);
    const previewButtons = document.querySelectorAll('.btn-preview');
    const selectButtons = document.querySelectorAll('.btn-select');
    const downloadButtons = document.querySelectorAll('.btn-download-trigger');
    const modal = document.getElementById('previewModal');
    const closeBtn = document.querySelector('.close-btn');
    const cvPreviewArea = document.getElementById('cvPreviewArea');
    const closeBtnFooter = document.querySelector('.btn-close-footer');

    const renderSkills = (skills) => skills.map(s => `<span class="skill-tag">${s}</span>`).join('');
    // ----------------------------------------
    // ส่วนที่ 1: ฟังก์ชันสำหรับสร้าง QR Code HTML
    // ----------------------------------------

    function generateQRCodeHtml(data) {
        const studentId = data.stu_id || '';
        
        // 💡 การแก้ไข: ควรเปลี่ยน localhost เป็น IP Address ของคุณหากต้องการสแกนจากมือถือ
        const IP_ADDRESS = 'localhost'; // หรือเปลี่ยนเป็น '192.168.x.x' 
        const fullLink = `http://${IP_ADDRESS}/cv_system/student_info.php?id=${studentId}`;
        
        console.log("Full Link for QR Code:", fullLink);

        // คืนค่า div สำหรับให้ qrcode.js สร้าง Canvas หรือ Table ในภายหลัง
        return `
            <div class="qr-code-box">
                <div id="qrCodeContainer-${studentId}"></div> 
                <span class="scan-id-text">Scan ID: ${studentId}</span>
            </div>
        `;
    }

    function generateCVHtml(templateId, data) {
        let detailItemStyle = "margin-bottom: 12px; line-height: 1.3; font-size: 0.95em;";
        let strongStyle = "display: block; font-size: 1.05em;";
        let pStyle = "margin: 0; font-size: 0.95em;";
        const qrCodeHtml = generateQRCodeHtml(data);
        
        const experienceHtml = data.work_experience.map(exp => `
            <div class="detail-item" style="${detailItemStyle}">
                <strong style="${strongStyle}">${exp.position} - ${exp.company}</strong>
                <span style="float: right; color: #666; font-size: 0.9em;">${exp.duration}</span>
                <p style="${pStyle}">รายละเอียด: ${exp.description}</p>
            </div>
        `).join('');

        const projectsHtml = data.projects.map(p => `<li><strong>${p.name}</strong>: ${p.description}</li>`).join('');
        const activitiesHtml = data.activities.map(a => `<li>${a}</li>`).join('');
        const projectAndActivitiesHtml = `<ul class="project-activity-list">${projectsHtml}${activitiesHtml}</ul>`;

        const educationContent = `
            <div class="detail-item" style="${detailItemStyle}">
                <strong style="${strongStyle}">อุดมศึกษา: ${data.edu_university}</strong>
                <span style="float: right; color: #666; font-size: 0.9em;">คาดจบ: ${data.edu_graduation_year}</span>
                <p style="${pStyle}">${data.edu_degree} (${data.edu_major})</p>
                <p style="margin: 0; font-size: 0.9em;">เกรดเฉลี่ย: ${data.edu_university_gpa}</p>
            </div>
            <div class="detail-item" style="${detailItemStyle}">
                <strong style="${strongStyle}">มัธยมศึกษา: ${data.edu_high_school}</strong>
                <p style="${pStyle}">แผนการเรียน: ${data.edu_high_school_plan}</p>
                <p style="margin: 0; font-size: 0.9em;">เกรดเฉลี่ย: ${data.edu_high_school_gpa}</p>
            </div>
        `;

        const contactContentWithIcons = `
            <div class="contact-info-item"><i class="fas fa-envelope"></i> อีเมล: ${data.email}</div>
            <div class="contact-info-item"><i class="fas fa-phone"></i> โทรศัพท์: ${data.phone}</div>
            <div class="contact-info-item"><i class="fas fa-home"></i> ที่อยู่: ${data.address}</div>
            <div class="contact-info-item"><i class="fas fa-link"></i> link: ${data.portfolio_link}</div>
        `;
        // **********************************
        // โค้ด HTML Templates (ไม่เปลี่ยนแปลง)
        // **********************************
        
        switch(templateId) {
            case 'modern-sidebar':
                return `
                    <div class="cv-red-theme cv-section">
                        <div class="header">
                            ${qrCodeHtml}
                            <div class="profile-photo" style="background-image:url('${data.profile_img}');"></div>
                            <h1>${data.name_th}</h1>
                            <h2>${data.job_title}</h2>
                        </div>

                        <h4>สรุปจุดเด่น</h4>
                        <p>${data.summary}</p>

                        <h4>ประสบการณ์</h4>
                        ${experienceHtml}

                        <h4>การศึกษา</h4>
                        ${educationContent}

                        <h4>ทักษะ</h4>
                        <div class="cv-prof-skills">
                            <div class="cv-skill-card">
                                <h5>Technical Skills</h5>
                                <p>${data.technical_skills.join(', ')}</p>
                            </div>

                            <div class="cv-skill-card">
                                <h5>Soft Skills</h5>
                                <p>${data.soft_skills.join(', ')}</p>
                            </div>
                        </div>

                        <h4>โปรเจกต์ / กิจกรรม</h4>
                        ${projectAndActivitiesHtml}

                        <h4>คำรับรอง</h4>
                        ${data.reference}

                        <h4>ข้อมูลติดต่อ</h4>
                        ${contactContentWithIcons}
                    </div>
                `;

            case 'professional-hybrid':
                return `
                    <div class="cv-pink-purple-theme cv-section">
                        <div class="header">
                            ${qrCodeHtml}
                            <div class="profile-photo" style="background-image:url('${data.profile_img}');"></div>
                            <h1>${data.name_th}</h1>
                            <h2>${data.job_title}</h2>
                        </div>

                        <h4>สรุปจุดเด่น</h4>
                        <p>${data.summary}</p>

                        <h4>ประสบการณ์</h4>
                        ${experienceHtml}

                        <h4>การศึกษา</h4>
                        ${educationContent}

                        <h4>ทักษะ</h4>
                        <div class="cv-prof-skills">
                            <div class="cv-skill-card">
                                <h5>Technical Skills</h5>
                                <p>${data.technical_skills.join(', ')}</p>
                            </div>

                            <div class="cv-skill-card">
                                <h5>Soft Skills</h5>
                                <p>${data.soft_skills.join(', ')}</p>
                            </div>
                        </div>

                        <h4>โปรเจกต์ / กิจกรรม</h4>
                        ${projectAndActivitiesHtml}

                        <h4>คำรับรอง</h4>
                        ${data.reference}

                        <h4>ข้อมูลติดต่อ</h4>
                        ${contactContentWithIcons}
                    </div>
                `;

            case 'minimalist-focus':
                return `
                    <div class="cv-minimalist-focus cv-section">
                        <div class="header">
                            ${qrCodeHtml}
                            <div class="profile-photo" style="background-image:url('${data.profile_img}');"></div>
                            <h1>${data.name_th}</h1>
                            <h2>${data.job_title}</h2>
                        </div>

                        <h4>สรุปจุดเด่น</h4>
                        <p>${data.summary}</p>

                        <h4>ประสบการณ์</h4>
                        ${experienceHtml}

                        <h4>การศึกษา</h4>
                        ${educationContent}

                        <h4>ทักษะ</h4>
                        <div class="cv-prof-skills">
                            <div class="cv-skill-card">
                                <h5>Technical Skills</h5>
                                <p>${data.technical_skills.join(', ')}</p>
                            </div>

                            <div class="cv-skill-card">
                                <h5>Soft Skills</h5>
                                <p>${data.soft_skills.join(', ')}</p>
                            </div>
                        </div>

                        <h4>โปรเจกต์ / กิจกรรม</h4>
                        ${projectAndActivitiesHtml}

                        <h4>คำรับรอง</h4>
                        ${data.reference}

                        <h4>ข้อมูลติดต่อ</h4>
                        ${contactContentWithIcons}
                    </div>
                `;

            default:
                return `<p>ไม่พบเทมเพลต</p>`;
        }
    }
    // ----------------------------------------
    // ส่วนที่ 3: ฟังก์ชันสำหรับดาวน์โหลด PDF (ไม่เปลี่ยนแปลง)
    // ----------------------------------------

    const downloadCV = (templateId, data) => {
        if (typeof html2pdf === 'undefined') {
            console.error('html2pdf.js library is not loaded. Please check your HTML script tag.');
            alert('เกิดข้อผิดพลาด: ไม่พบไลบรารีสำหรับสร้าง PDF');
            return;
        }

        const element = document.createElement('div');
        element.innerHTML = generateCVHtml(templateId, data);
        
        const studentId = data.stu_id || '';
        const qrContainer = element.querySelector(`#qrCodeContainer-${studentId}`);
        const IP_ADDRESS = 'localhost'; 
        const fullLink = `http://${IP_ADDRESS}/cv_system/student_info.php?id=${studentId}`;

        if (qrContainer) {
            new QRCode(qrContainer, {
                text: fullLink,
                width: 100,
                height: 100,
            });
        }
        
        const options = {
            margin: [20, 10, 20, 10], 
            filename: `CV_${data.name_th}_${templateId}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 }, 
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        html2pdf().set(options).from(element.querySelector('.cv-section')).save();
    };

    // ----------------------------------------
    // ส่วนที่ 4: Event Listeners (แก้ไขแล้ว)
    // ----------------------------------------
    
    // Preview CV (ทำงานเหมือนเดิม)
    previewButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const templateId = btn.dataset.template;
            cvPreviewArea.innerHTML = '';
            
            cvPreviewArea.innerHTML = generateCVHtml(templateId, data);
            
            const studentId = data.stu_id || '';
            const qrContainer = document.getElementById(`qrCodeContainer-${studentId}`);
            const IP_ADDRESS = 'localhost'; 
            const fullLink = `http://${IP_ADDRESS}/cv_system/student_info.php?id=${studentId}`;

            if (qrContainer) {
                new QRCode(qrContainer, {
                    text: fullLink,
                    width: 100,
                    height: 100,
                });
            }
            
            modal.style.display = 'block';

            // โค้ดปรับ Scale ใน Modal (ไม่เปลี่ยนแปลง)
            const cvContent = cvPreviewArea.querySelector('.cv-section'); 
            if(cvContent) {
                setTimeout(() => {
                    const contentWidth = cvContent.offsetWidth; 
                    const a4Width = 794; 
                    const safetyFactor = 0.97; 
                    let scale = (a4Width / contentWidth) * safetyFactor; 
                    if (scale > 1) { scale = 1; }
                    cvContent.style.transformOrigin = 'top center';
                    cvContent.style.transform = `scale(${scale})`;
                }, 50);
            }
        });
    });

    // 💡 NEW: Event Listener สำหรับปุ่ม ดาวน์โหลด (.btn-download-trigger)
    downloadButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const templateId = btn.dataset.template;
            alert(`กำลังเริ่มดาวน์โหลด CV เทมเพลต "${templateId}" ในรูปแบบ PDF...`);
            downloadCV(templateId, data);
        });
    });


    // 💡 MODIFIED: Event Listener สำหรับปุ่ม เลือก (.btn-select) - บันทึกลง DB เท่านั้น
    selectButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const templateId = btn.dataset.template;
            const confirmSelect = confirm(`คุณต้องการเลือกเทมเพลต "${templateId}" และบันทึกลงระบบใช่หรือไม่?`);
            if (!confirmSelect) return;

            try {
                // 1. บันทึกเทมเพลตลงฐานข้อมูล
                const res = await fetch('save_template.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `template_name=${encodeURIComponent(templateId)}`
                });
                const result = await res.json();
                
                if (result.success) {
                    alert(`บันทึกเทมเพลต "${templateId}" เรียบร้อยแล้ว`);
                } else {
                    alert('เกิดข้อผิดพลาดในการบันทึกเทมเพลต: ' + result.message);
                }
            } catch (err) {
                alert('เกิดข้อผิดพลาด: ' + err);
            }
        });
    });

    // Close modal (กากบาท)
    closeBtn.addEventListener('click', () => modal.style.display = 'none');
    
    // Close modal (ปุ่มที่ footer - ถ้ามี)
    if (closeBtnFooter) {
        closeBtnFooter.addEventListener('click', () => modal.style.display = 'none');
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', e => {
        if (e.target === modal) modal.style.display = 'none';
    });

});