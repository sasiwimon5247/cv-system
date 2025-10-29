document.addEventListener('DOMContentLoaded', () => {
    const data = USER_CV_DATA; 
    console.log(USER_CV_DATA);
    const previewButtons = document.querySelectorAll('.btn-preview');
    const selectButtons = document.querySelectorAll('.btn-select');
    const modal = document.getElementById('previewModal');
    const closeBtn = document.querySelector('.close-btn');
    const cvPreviewArea = document.getElementById('cvPreviewArea');
    const closeBtnFooter = document.querySelector('.btn-close-footer');

    const renderSkills = (skills) => skills.map(s => `<span class="skill-tag">${s}</span>`).join('');

    function generateQRCodeHtml(data) {
        const studentName = data.name_th || '';
        const studentId = data.stu_id ||''; 
        const universityName = data.edu_university || '';

        // 💡 การแก้ไข: แก้ไขการต่อ String และกำหนด URL หลักเป็น Localhost
        const fullLink = 
            `http://localhost/cv_system/student_info.php?id=${studentId}`;
        console.log("Full Link for QR Code:", fullLink);
        // สร้าง URL สำหรับ QR Code Image
        const qrCodeUrl = 
            `https://chart.googleapis.com/chart?cht=qr&chs=100x100&chl=${encodeURIComponent(fullLink)}`;
        
        // แสดงผล
        return `
            <div class="qr-code-box">
            <img src="${qrCodeUrl}" alt="Student QR Code">
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

    // --- Event Listeners ---
    // Preview CV
    previewButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const templateId = btn.dataset.template;
            cvPreviewArea.innerHTML = '';
            
            // Generate และใส่ HTML
            cvPreviewArea.innerHTML = generateCVHtml(templateId, data);

            // แสดง modal
            modal.style.display = 'block';

            // --- ปรับ scale ให้พอดี A4 ---
            const cvContent = cvPreviewArea.querySelector('.cv-section'); 
            if(cvContent) {
                setTimeout(() => {
                    // ... (โค้ด reset transform) ...
                    
                    const contentWidth = cvContent.offsetWidth; // ควรจะเป็น 794px
                    const a4Width = 794; 
                    
                    // 💡 ปรับปรุง: ลด Scale ลงเป็น 0.97 (ย่อลง 3%) 
                    // เพื่อให้มี Margin 20px รอบ CV และยังพอดีกับพื้นที่ Modal
                    const safetyFactor = 0.97; 

                    let scale = (a4Width / contentWidth) * safetyFactor; 
                    
                    if (scale > 1) {
                        scale = 1;
                    }

                    cvContent.style.transformOrigin = 'top center';
                    cvContent.style.transform = `scale(${scale})`;

                }, 50);
            }
        });
    });


    // Close modal (ปุ่ม x ด้านบน)
    closeBtn.addEventListener('click', () => modal.style.display = 'none');
    
    // 💡 เพิ่ม: Close modal (ปุ่มปิดที่ Footer)
    if (closeBtnFooter) {
        closeBtnFooter.addEventListener('click', () => modal.style.display = 'none');
    }

    // Close modal when clicking outside
    window.addEventListener('click', e => {
        if (e.target === modal) modal.style.display = 'none';
    });

    // Select and Save Template
    selectButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const templateId = btn.dataset.template;
            const confirmSelect = confirm(`คุณต้องการเลือกเทมเพลต "${templateId}" ใช่หรือไม่?`);
            if (!confirmSelect) return;

            try {
                const res = await fetch('save_template.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `template_name=${encodeURIComponent(templateId)}`
                });
                const result = await res.json();
                if (result.success) {
                    alert(`บันทึกเทมเพลต "${templateId}" เรียบร้อยแล้ว`);
                } else {
                    alert('เกิดข้อผิดพลาด: ' + result.message);
                }
            } catch (err) {
                alert('เกิดข้อผิดพลาด: ' + err);
            }
        });
    });

});