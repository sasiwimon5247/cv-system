document.addEventListener('DOMContentLoaded', () => {
    // ใช้ USER_CV_DATA
    const data = USER_CV_DATA; 
    console.log(USER_CV_DATA);

    // 💡 FIX 1: กำหนด RECOMMENDATION_DATA จากข้อมูลที่ดึงมาจาก PHP
    // ข้อมูลถูกจัดเก็บใน USER_CV_DATA.reference_text และ USER_CV_DATA.reference_teacher
    const RECOMMENDATION_DATA = data.reference_text ? {
        // ใช้ certificate_text เพื่อให้สอดคล้องกับที่เรียกใช้ใน generateRecommendationHtml() เดิม
        certificate_text: data.reference_text,
        teacher_name: data.reference_teacher || 'ผู้ให้คำรับรองไม่ระบุ'
    } : null;
    
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
        const IP_ADDRESS = 'localhost'; // หรือเปลี่ยนเป็น '192.168.x.x' 
        const fullLink = `http://${IP_ADDRESS}/cv_system/student_info.php?id=${studentId}`;
        
        console.log("Full Link for QR Code:", fullLink);

        return `
            <div class="qr-code-box">
                <div id="qrCodeContainer-${studentId}"></div> 
                <span class="scan-id-text">Scan ID: ${studentId}</span>
            </div>
        `;
    }

    // ----------------------------------------
    // UPDATED: ฟังก์ชันสร้าง HTML สำหรับส่วนคำรับรอง
    // 💡 FIX 2: แก้ไขการเข้าถึง property ให้ใช้ .certificate_text และ .teacher_name จาก RECOMMENDATION_DATA
    // ----------------------------------------
    function generateRecommendationHtml() {
        if (!RECOMMENDATION_DATA || !RECOMMENDATION_DATA.certificate_text) {
            return ''; // เว้นว่างไว้หากไม่มีข้อมูลคำรับรอง
        }
        
        return `
            <h4>คำรับรอง</h4>
            <div class="recommendation-content">
                <p class="recommendation-text">
                    "${RECOMMENDATION_DATA.certificate_text}"
                </p>
                <p class="recommendation-teacher">
                    <strong>จาก:</strong> ${RECOMMENDATION_DATA.teacher_name}
                </p>
            </div>
        `;
    }

    function generateCVHtml(templateId, data) {
        // 💡 FIX 3: ลบตัวแปร Inline Style ที่ไม่จำเป็นออก
        // let detailItemStyle = "margin-bottom: 12px; line-height: 1.3; font-size: 0.95em;";
        // let strongStyle = "display: block; font-size: 1.05em;";
        // let pStyle = "margin: 0; font-size: 0.95em;";
        
        const qrCodeHtml = generateQRCodeHtml(data);
        const recommendationHtml = generateRecommendationHtml(); 
        
        const experienceHtml = data.work_experience.map(exp => `
            <div class="detail-item">
                <strong>${exp.position} - ${exp.company}</strong>
                <span class="duration">${exp.duration}</span>
                <p>รายละเอียด: ${exp.description}</p>
            </div>
        `).join('');

        const educationContent = `
            <div class="detail-item">
                <strong>อุดมศึกษา: ${data.edu_university}</strong>
                <span class="duration">คาดจบ: ${data.edu_graduation_year}</span>
                <p>${data.edu_degree} (${data.edu_major})</p>
                <p style="margin: 0; font-size: 0.9em;">เกรดเฉลี่ย: ${data.edu_university_gpa}</p>
            </div>
            <div class="detail-item">
                <strong>มัธยมศึกษา: ${data.edu_high_school}</strong>
                <p>แผนการเรียน: ${data.edu_high_school_plan}</p>
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
        // โค้ด HTML Templates (มีการจัดเรียงส่วน คำรับรอง และ ข้อมูลติดต่อ)
        // 💡 FIX 4: ตรวจสอบให้แน่ใจว่าใช้ recommendationHtml ที่ถูกต้อง
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

                        <h4>โปรเจค</h4>
                        <p>${data.project}</p>

                        <h4>กิจกรรม</h4>
                        <p>${data.activity}</p>

                        ${recommendationHtml} 

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

                        <h4>โปรเจค</h4>
                        <p>${data.project}</p>

                        <h4>กิจกรรม</h4>
                        <p>${data.activity}</p>

                        ${recommendationHtml} 

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

                        <h4>โปรเจค</h4>
                        <p>${data.project}</p>

                        <h4>กิจกรรม</h4>
                        <p>${data.activity}</p>

                        ${recommendationHtml} 

                        <h4>ข้อมูลติดต่อ</h4>
                        ${contactContentWithIcons}
                    </div>
                `;

            default:
                return `<p>ไม่พบเทมเพลต</p>`;
        }
    }
    
    // ----------------------------------------
    // ส่วนที่ 3: ฟังก์ชันสำหรับดาวน์โหลด PDF (ไม่มีการเปลี่ยนแปลง)
    // ----------------------------------------

    const downloadCV = (templateId, data) => {
        if (typeof html2pdf === 'undefined') {
            console.error('html2pdf.js library is not loaded. Please check your HTML script tag.');
            // 💡 Note: Using a console message instead of alert for better UI experience
            console.log('เกิดข้อผิดพลาด: ไม่พบไลบรารีสำหรับสร้าง PDF');
            return;
        }

        const element = document.createElement('div');
        element.innerHTML = generateCVHtml(templateId, data);
        
        const studentId = data.stu_id || '';
        const qrContainer = element.querySelector(`#qrCodeContainer-${studentId}`);
        const IP_ADDRESS = 'localhost'; 
        const fullLink = `http://${IP_ADDRESS}/cv_system/student_info.php?id=${studentId}`;

        if (qrContainer) {
            // ต้องสร้าง QR Code ใหม่ก่อนดาวน์โหลด เพราะ html2pdf จะไม่รอการสร้างจาก Preview Modal
            new QRCode(qrContainer, {
                text: fullLink,
                width: 100,
                height: 100,
            });
            // ให้เวลากับการเรนเดอร์ QR Code ก่อนทำการดาวน์โหลด (ถ้าต้องการความแม่นยำสูง)
            setTimeout(() => {
                const options = {
                    margin: [20, 10, 20, 10], 
                    filename: `CV_${data.name_th}_${templateId}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 }, 
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };
                
                html2pdf().set(options).from(element.querySelector('.cv-section')).save();
            }, 100); // หน่วงเวลาเล็กน้อย
        } else {
             const options = {
                margin: [2, 5, 5, 5], 
                filename: `CV_${data.name_th}_${templateId}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 }, 
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            html2pdf().set(options).from(element.querySelector('.cv-section')).save();
        }
    };
    
    // ----------------------------------------
    // ส่วนที่ 4: Event Listeners (ไม่มีการเปลี่ยนแปลงตรรกะ)
    // ----------------------------------------

    // Preview CV
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
                // สร้าง QR Code เมื่อแสดง Modal Preview
                qrContainer.innerHTML = ''; // Clear previous QR
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

    // Event Listener สำหรับปุ่ม ดาวน์โหลด (.btn-download-trigger)
    downloadButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const templateId = btn.dataset.template;
            // 💡 Note: Using console.log instead of alert
            console.log(`กำลังเริ่มดาวน์โหลด CV เทมเพลต "${templateId}" ในรูปแบบ PDF...`);
            downloadCV(templateId, data);
        });
    });

    // Close modal (ปุ่ม x ด้านบน)
    closeBtn.addEventListener('click', () => modal.style.display = 'none');
    
    // Close modal (ปุ่มปิดที่ Footer)
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
            // 💡 Note: Using a mock confirm/console log instead of window.confirm/alert
            const confirmSelect = true; 
            if (!confirmSelect) return;

            try {
                const res = await fetch('save_template.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `template_name=${encodeURIComponent(templateId)}`
                });
                const result = await res.json();
                if (result.success) {
                    console.log(`บันทึกเทมเพลต "${templateId}" เรียบร้อยแล้ว`);
                } else {
                    console.error('เกิดข้อผิดพลาด: ' + result.message);
                }
            } catch (err) {
                console.error('เกิดข้อผิดพลาด: ' + err);
            }
        });
    });

});