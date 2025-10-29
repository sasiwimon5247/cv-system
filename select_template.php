<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกเทมเพลต CV 1 หน้า A4 (แก้ไขข้อผิดพลาด Template 3)</title>

    <script>
        const USER_CV_DATA = {
            'profile_img': 'https://via.placeholder.com/250x250?text=Photo', // URL รูปโปรไฟล์ของคุณ
            'name_th': 'ณัฐพงศ์ สุวรรณทะ',
            'job_title': 'นักพัฒนา Web Application และผู้เชี่ยวชาญเครือข่าย',
            'email': 'sasiwimont65@nu.ac.th',
            'phone': '095-306-9514',
            'address': '7/1 หมู่ 3 ต. วาโก้ จ.สุโขทัย',
            'portfolio_link': 'https://github.com/Natthaphong-S', 
            'summary': 'นักศึกษาปี 4 วิศวกรรมคอมพิวเตอร์ ผู้มีความเชี่ยวชาญด้านการพัฒนา Full-Stack และเครือข่ายคอมพิวเตอร์ มีความมุ่งมั่นในการสร้างสรรค์โซลูชันที่มีประสิทธิภาพและปรับใช้เทคโนโลยีใหม่ๆ เพื่อแก้ไขปัญหาในโลกแห่งความเป็นจริง',
            
            'edu_high_school': 'โรงเรียนสวรรค์อนันต์วิทยา',
            'edu_high_school_plan': 'แผนการเรียน วิทย์-คณิต',
            'edu_high_school_gpa': '3.45',
            'edu_university': 'มหาวิทยาลัยแม่ฟ้าหลวง',
            'edu_degree': 'ปริญญาตรี (วศ.บ.)',
            'edu_faculty': 'สำนักวิชาเทคโนโลยีสารสนเทศ',
            'edu_major': 'วิศวกรรมคอมพิวเตอร์',
            'edu_graduation_year': 'พฤษภาคม 2568',
            'edu_university_gpa': '3.85 (ปัจจุบัน)',

            'work_experience': [{ title: 'นักศึกษาฝึกงานด้าน IoT และ Web Development', company: 'บริษัท โซลูชันเทคโนโลยี จำกัด', duration: 'มี.ค. 2567 - ปัจจุบัน', details: 'รับผิดชอบการพัฒนาโมดูลควบคุมอุปกรณ์ IoT ด้วย ESP32 และสร้าง Dashboard สำหรับแสดงผลข้อมูลแบบ Real-time ด้วย ReactJS' }],
            'technical_skills': ['Java', 'C#', 'Python', 'HTML/CSS', 'JavaScript (ReactJS)', 'PHP (Laravel)', 'SQL', 'GNS3', 'Cisco Networking'],
            'soft_skills': ['การทำงานเป็นทีม', 'การสื่อสารที่มีประสิทธิภาพ', 'การจัดการเวลา', 'ความสามารถในการปรับตัว'],
            'projects': [ { name: 'ระบบสำรองห้องพัก (Web Application)', details: 'พัฒนา Backend ด้วย PHP และ MySQL' }, { name: 'Network and Firewall Simulation Project', details: 'ออกแบบและนำระบบเครือข่ายที่ปลอดภัยมาใช้' } ],
            'reference': 'ผู้ช่วยศาสตราจารย์ ดร. ชยพล คำป่อย (chayapol.k@mfu.ac.th)'
        };
    </script>

    <style>
        /* Define New Color Palette (Modern & Professional) */
        :root {
            --color-primary: #00796B;   /* Teal/Dark Cyan - Accent Color */
            --color-secondary: #263238; /* Charcoal/Dark Blue - Main Text/Headers */
            --color-light-bg: #F4F6F7;  /* Very Light Grey/Off-White - Background for sidebars/boxes */
            --color-text: #37474F;      /* Dark Grey - Body Text */
            --color-border: #CFD8DC;    /* Light Border */
        }
        
        /* General Styles */
        body { font-family: 'Sukhumvit Set', 'Arial', sans-serif; background-color: #f4f4f9; color: var(--color-text); margin: 0; padding: 20px; text-align: center; }
        .template-container { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; }
        .template-card { background-color: #fff; border-radius: 12px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); padding: 25px; width: 320px; text-align: left; border-top: 5px solid var(--color-primary); }
        .btn-preview { background-color: #6c757d; color: white; margin-bottom: 5px; padding: 10px; width: 100%; border: none; border-radius: 4px;}
        .btn-select { background-color: var(--color-primary); color: white; padding: 10px; width: 100%; border: none; border-radius: 4px;}
        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8); }
        .modal-content { background-color: #fefefe; margin: 2% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 1100px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close-btn:hover, .close-btn:focus { color: #000; text-decoration: none; cursor: pointer; }

        /* --- KEY: A4 SIMULATION (Global) --- */
        #cvPreviewArea {
            width: 794px; 
            height: 1123px; 
            margin: 20px auto;
            padding: 35px; 
            background-color: white;
            border: 1px solid var(--color-border);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            font-size: 11pt; /* Base font for 1 & 2 */
            line-height: 1.5; 
            text-align: left;
            box-sizing: border-box;
        }
        
        /* Shared Styles for CV Sections */
        .cv-section h4 {
            font-size: 1.25em; 
            font-weight: bold;
            margin-top: 25px; 
            margin-bottom: 8px;
            padding-bottom: 2px;
        }
        .detail-item {
            margin-bottom: 15px; 
            line-height: 1.4;
        }
        .detail-item strong {
            display: block;
            font-size: 1.1em; 
        }
        
        /* Icon Styling */
        .contact-info-item {
            display: flex;
            align-items: flex-start;
            font-size: 1em;
            margin-bottom: 6px; 
        }
        .icon {
            margin-right: 8px;
            font-size: 1.1em;
            width: 20px;
            text-align: center;
            color: var(--color-primary); 
        }
        
        /* --- 1. MODERN-SIDEBAR STYLES --- */
        .cv-modern-sidebar { display: flex; height: 100%; }
        .cv-modern-sidebar .cv-sidebar {
            width: 33%; background-color: var(--color-secondary); color: white;
            padding: 10px 15px 15px 15px; box-sizing: border-box; font-size: 1em; line-height: 1.4;
        }
        .cv-modern-sidebar .cv-main-content { width: 67%; padding: 0 20px; box-sizing: border-box; }
        .cv-modern-sidebar h1 { color: var(--color-secondary); font-size: 2.8em; margin-bottom: 5px; }
        .cv-modern-sidebar h2 { color: var(--color-text); border-bottom: 2px solid var(--color-border); padding-bottom: 5px; margin-top: 0; }
        .cv-modern-sidebar .cv-sidebar h4 { color: var(--color-primary); border-bottom: 1px solid var(--color-primary); margin-top: 20px; font-size: 1.15em; }
        .cv-modern-sidebar .cv-main-content h4 { color: var(--color-secondary); border-bottom: 1px solid var(--color-primary); }
        .cv-modern-sidebar .profile-photo { 
            width: 78px; height: 104px; border-radius: 5px; margin: 10px auto 10px auto; 
            display: block; border: 3px solid var(--color-primary); background-size: cover; background-position: center;
        }
        .cv-modern-sidebar .cv-sidebar a { color: var(--color-primary); }

        /* --- 2. PROFESSIONAL-HYBRID STYLES --- */
        .cv-professional-hybrid { height: 100%; display: flex; flex-direction: column; }
        .cv-professional-hybrid .header { border-bottom: 3px solid var(--color-primary); padding-bottom: 8px; margin-bottom: 20px; }
        .cv-professional-hybrid h1 { color: var(--color-secondary); font-size: 3em; margin: 0; text-align: left; }
        .cv-professional-hybrid h2 { color: var(--color-primary); font-size: 1.5em; margin: 0; text-align: left; }
        .cv-professional-hybrid .summary-box { margin-bottom: 20px; padding: 0 0 10px 0; font-style: italic; border-bottom: 1px dashed var(--color-border); }
        .cv-professional-hybrid .main-cols { display: flex; gap: 30px; height: 100%; }
        .cv-professional-hybrid .left-col { width: 68%; padding-right: 15px; }
        .cv-professional-hybrid .right-col { width: 32%; background-color: var(--color-light-bg); padding: 15px; border-radius: 5px; }
        .cv-professional-hybrid h4 { color: var(--color-secondary); border-bottom: 1px solid var(--color-border); margin-top: 20px; padding-bottom: 3px; font-size: 1.2em; }
        .cv-professional-hybrid .right-col h4 { color: var(--color-primary); }
        .skill-tag { display:inline-block; background-color:var(--color-border); padding: 4px 8px; margin: 4px 4px 4px 0; border-radius: 4px; font-size: 0.95em;}

        /* --- 3. MINIMALIST-FOCUS STYLES --- */
        .cv-minimalist-focus { height: 100%; padding: 0 30px; font-size: 10.5pt; line-height: 1.4; } /* Smaller base font for template 3 */
        .cv-minimalist-focus .header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary); }
        .cv-minimalist-focus h1 { font-size: 2.8em; color: var(--color-secondary); margin-bottom: 3px; }
        .cv-minimalist-focus h2 { font-size: 1.3em; color: var(--color-primary); margin: 0; }
        .cv-minimalist-focus h4 { color: var(--color-secondary); border-bottom: 1px solid var(--color-primary); margin-top: 25px; padding-bottom: 3px; font-size: 1.15em; }
        .cv-minimalist-focus .footer-contact { margin-top: 25px; padding-top: 8px; border-top: 1px solid var(--color-border); text-align: center; }
        .cv-minimalist-focus .footer-contact span, .cv-minimalist-focus .footer-contact p { font-size: 0.95em;}
    </style>
</head>
<body>

    <header>
        <h1>เลือกเทมเพลตสำหรับเรซูเม่ (CV)</h1>
        <p>โปรดเลือกเทมเพลตที่ถูกใจ **ข้อมูลของคุณจะถูกจัดรูปแบบให้พอดีเต็ม 1 หน้า A4**</p>
    </header>
    
    <hr>

    <div class="template-container">
        
        <div class="template-card" data-template-id="modern-sidebar">
            <h3>1. ทันสมัย-แถบข้าง (33:67) 🟢 (ปรับขนาด/ตีมสี)</h3>
            <p class="description">**ตีมใหม่:** เน้นความชัดเจนด้วยสีน้ำเงินเข้ม/ฟ้าคราม</p>
            <button class="btn btn-preview" data-template="modern-sidebar">ดูตัวอย่างด้วยข้อมูลของฉัน (A4)</button>
            <button class="btn btn-select" data-template="modern-sidebar">เลือกเทมเพลตนี้</button>
        </div>

        <div class="template-card" data-template-id="professional-hybrid">
            <h3>2. มืออาชีพ-ผสม (68:32) 🌟 (ปรับขนาด/ตีมสี)</h3>
            <p class="description">**ตีมใหม่:** โครงสร้างแข็งแกร่ง ชื่อ/ตำแหน่งเด่นชัดยิ่งขึ้น</p>
            <button class="btn btn-preview" data-template="professional-hybrid">ดูตัวอย่างด้วยข้อมูลของฉัน (A4)</button>
            <button class="btn btn-select" data-template="professional-hybrid">เลือกเทมเพลตนี้</button>
        </div>
        
        <div class="template-card" data-template-id="minimalist-focus">
            <h3>3. มินิมอล-เน้นเนื้อหา (100%) ✨ (แก้ไขบั๊กแล้ว)</h3>
            <p class="description">**ตีมใหม่:** สะอาดตา แต่ใช้สีฟ้าคราม/น้ำเงินเข้มเน้นความสำคัญ</p>
            <button class="btn btn-preview" data-template="minimalist-focus">ดูตัวอย่างด้วยข้อมูลของฉัน (A4)</button>
            <button class="btn btn-select" data-template="minimalist-focus">เลือกเทมเพลตนี้</button>
        </div>

    </div>
    
    <div id="previewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>ตัวอย่างเรซูเม่ (ขนาด A4)</h2>
            <div id="cvPreviewArea">
                </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const previewButtons = document.querySelectorAll('.btn-preview');
            const selectButtons = document.querySelectorAll('.btn-select');
            const modal = document.getElementById('previewModal');
            const closeBtn = document.querySelector('.close-btn');
            const cvPreviewArea = document.getElementById('cvPreviewArea');
            const data = USER_CV_DATA; 

            // Helper functions to generate sections
            const renderSkills = (skills) => skills.map(s => `<span class="skill-tag">${s}</span>`).join('');
            
            // --- MAIN FUNCTION: Generate CV HTML (Corrected Scope Issue) ---
            function generateCVHtml(templateId, data) {
                // Initialize styles with default (for Template 1 & 2)
                let detailItemStyle = "margin-bottom: 15px; line-height: 1.4; font-size: 1em;"; 
                let strongStyle = "display: block; font-size: 1.1em;";
                let pStyle = "margin: 0; font-size: 1em;";

                // Override styles for Template 3 (Minimalist-Focus) to ensure smaller text
                if (templateId === 'minimalist-focus') {
                    detailItemStyle = "margin-bottom: 12px; line-height: 1.3; font-size: 0.95em;";
                    strongStyle = "display: block; font-size: 1.05em;";
                    pStyle = "margin: 0; font-size: 0.95em;";
                }

                const experienceHtml = data.work_experience.map(exp => `
                    <div class="detail-item" style="${detailItemStyle}">
                        <strong style="${strongStyle}">${exp.title} - ${exp.company}</strong>
                        <span style="float: right; color: #666; font-size: 0.9em;">${exp.duration}</span>
                        <p style="${pStyle}">รายละเอียด: ${exp.details}</p>
                    </div>
                `).join('');

                const projectsHtml = data.projects.map(p => `
                    <li><strong>${p.name}</strong>: ${p.details}</li>
                `).join('');
                
                // Content Blocks
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
                
                // Contact List with Icons
                const contactContentWithIcons = `
                    <div class="contact-info-item">
                        <span class="icon">&#9993;</span>
                        <p style="margin: 0; line-height: 1.2; overflow-wrap: break-word;">**อีเมล:** ${data.email}</p>
                    </div>
                    <div class="contact-info-item">
                        <span class="icon">&#9742;</span>
                        <p style="margin: 0; line-height: 1.2;">**โทรศัพท์:** ${data.phone}</p>
                    </div>
                    <div class="contact-info-item">
                        <span class="icon">&#127969;</span>
                        <p style="margin: 0; line-height: 1.2;">**ที่อยู่:** ${data.address}</p>
                    </div>
                    <div class="contact-info-item">
                        <span class="icon">&#128187;</span>
                        <p style="margin: 0; line-height: 1.2;">**Github/Portfolio:** <a href="${data.portfolio_link}" style="color: var(--color-primary);">[Link]</a></p>
                    </div>
                `;
                
                // 1. MODERN-SIDEBAR (33:67)
                if (templateId === 'modern-sidebar') {
                    return `
                        <div class="cv-modern-sidebar cv-section">
                            <div class="cv-sidebar">
                                <div class="profile-photo" style="background-image: url('${data.profile_img}');"></div>
                                
                                <h4 style="text-align: center;">ข้อมูลติดต่อ</h4>
                                <div style="padding: 0 5px; font-size: 1.05em; line-height: 1.4;">
                                    <div style="margin-bottom: 5px;"><span style="color:var(--color-primary);">&#9742;</span> ${data.phone}</div>
                                    <div style="margin-bottom: 5px;"><span style="color:var(--color-primary);">&#9993;</span> ${data.email}</div>
                                    <div style="margin-bottom: 5px;"><span style="color:var(--color-primary);">&#127969;</span> ${data.address}</div>
                                    <div style="margin-bottom: 10px;"><span style="color:var(--color-primary);">&#128187;</span> <a href="${data.portfolio_link}" style="color: var(--color-primary); text-decoration: none;">[Github/Portfolio]</a></div>
                                </div>

                                <h4 style="text-align: center;">Technical Skills</h4>
                                <ul style="list-style: none; padding-left: 10px; font-size: 1.05em;">${data.technical_skills.map(s => `<li>• ${s}</li>`).join('')}</ul>
                                
                                <h4 style="text-align: center;">Soft Skills</h4>
                                <ul style="list-style: none; padding-left: 10px; font-size: 1.05em;">${data.soft_skills.map(s => `<li>• ${s}</li>`).join('')}</ul>
                                
                                <h4 style="text-align: center;">คำรับรอง</h4>
                                <p style="font-size: 1em; text-align: center;">${data.reference}</p>
                            </div>
                            
                            <div class="cv-main-content">
                                <div>
                                    <h1>${data.name_th}</h1>
                                    <h2>${data.job_title}</h2>
                                    
                                    <h4 class="cv-section">สรุปจุดเด่น (Summary)</h4>
                                    <p style="font-size: 1.05em; line-height: 1.5;">${data.summary}</p>
                                    
                                    <h4 class="cv-section">ประสบการณ์การทำงาน/ฝึกงาน</h4>
                                    ${experienceHtml}
                                    
                                    <h4 class="cv-section">ประวัติการศึกษา</h4>
                                    ${educationContent}
                                    
                                    <h4 class="cv-section">โปรเจกต์/กิจกรรม</h4>
                                    <ul style="font-size: 1em; margin-top: 5px; padding-left: 20px;">
                                        ${projectsHtml}
                                        <li><strong>กิจกรรม:</strong> เข้าร่วมชมรมนักพัฒนาซอฟต์แวร์ (2565-2567)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                } 
                
                // 2. PROFESSIONAL-HYBRID (68:32)
                else if (templateId === 'professional-hybrid') {
                    return `
                        <div class="cv-professional-hybrid cv-section">
                            <div class="header">
                                <h1>${data.name_th}</h1>
                                <h2>${data.job_title}</h2>
                            </div>
                            
                            <div class="summary-box">
                                <h4 style="margin: 0; padding-bottom: 5px; border-bottom: 1px solid var(--color-primary); color: var(--color-secondary); font-size: 1.2em; font-weight: bold;">สรุปจุดเด่น (Summary)</h4>
                                <p style="font-size: 1.1em; margin: 10px 0 0 0; line-height: 1.5;">${data.summary}</p>
                            </div>
                            
                            <div class="main-cols">
                                <div class="left-col">
                                    <h4 class="cv-section">ประสบการณ์การทำงาน/ฝึกงาน 💼</h4>
                                    ${experienceHtml}

                                    <h4 class="cv-section">ประวัติการศึกษา 🎓</h4>
                                    ${educationContent}

                                    <h4 class="cv-section">โปรเจกต์/กิจกรรมที่เกี่ยวข้อง 🚀</h4>
                                    <ul style="font-size: 1em; margin-top: 5px; padding-left: 20px; list-style-type: square;">
                                        ${projectsHtml}
                                        <li><strong>กิจกรรม:</strong> เข้าร่วมชมรมนักพัฒนาซอฟต์แวร์ (2565-2567)</li>
                                    </ul>
                                </div>
                                
                                <div class="right-col">
                                    <h4 class="cv-section" style="color: var(--color-primary);">ข้อมูลติดต่อ 📞</h4>
                                    <div style="font-size: 1.05em; line-height: 1.4;">${contactContentWithIcons}</div>

                                    <h4 class="cv-section" style="color: var(--color-primary);">Technical Skills 💻</h4>
                                    <div style="margin-top: 5px; line-height: 1.5;">${renderSkills(data.technical_skills)}</div>

                                    <h4 class="cv-section" style="color: var(--color-primary);">Soft Skills 🧑‍🤝‍🧑</h4>
                                    <div style="margin-top: 5px; line-height: 1.5;">${renderSkills(data.soft_skills)}</div>
                                    
                                    <h4 class="cv-section" style="color: var(--color-primary);">บุคคลอ้างอิง</h4>
                                    <p style="font-size: 1em; margin-bottom: 0;">${data.reference}</p>
                                </div>
                            </div>
                        </div>
                    `;
                } 
                
                // 3. MINIMALIST-FOCUS (100%) - FIXED
                else if (templateId === 'minimalist-focus') {
                    return `
                        <div class="cv-minimalist-focus cv-section">
                            <div class="header">
                                <div style="width: 110px; height: 110px; border-radius: 50%; background-color: var(--color-light-bg); margin: 0 auto 8px auto; border: 3px solid var(--color-primary); background-image: url('${data.profile_img}'); background-size: cover; background-position: center;">
                                </div>
                                <h1>${data.name_th}</h1>
                                <h2>${data.job_title}</h2>
                            </div>
                            
                            <h4 class="cv-section">สรุปจุดเด่น 🌟</h4>
                            <p style="font-size: 1em; margin: 5px 0 15px 0; line-height: 1.4;">${data.summary}</p>

                            <h4 class="cv-section">ประสบการณ์การทำงาน/ฝึกงาน 💼</h4>
                            ${experienceHtml}

                            <h4 class="cv-section">ประวัติการศึกษา 🎓</h4>
                            ${educationContent}

                            <h4 class="cv-section">ทักษะ 🛠️</h4>
                            <div style="display: flex; gap: 30px; margin-top: 8px;">
                                <div style="width: 50%;">
                                    <p style="font-weight: bold; margin-bottom: 5px; color: var(--color-secondary);">Technical Skills:</p>
                                    <p style="font-size: 0.95em; line-height: 1.3;">${data.technical_skills.map(s => `• ${s}`).join('<br>')}</p>
                                </div>
                                <div style="width: 50%;">
                                    <p style="font-weight: bold; margin-bottom: 5px; color: var(--color-secondary);">Soft Skills:</p>
                                    <p style="font-size: 0.95em; line-height: 1.3;">${data.soft_skills.map(s => `• ${s}`).join('<br>')}</p>
                                    <p style="font-weight: bold; margin-top: 15px; margin-bottom: 5px; color: var(--color-secondary);">โปรเจกต์เด่น:</p>
                                    <ul style="list-style: none; padding-left: 0; margin: 0; font-size: 0.95em;">${projectsHtml}</ul>
                                </div>
                            </div>
                            
                            <div class="footer-contact">
                                <p style="margin-bottom: 8px; font-weight: bold; color: var(--color-primary); font-size: 1em;">ข้อมูลติดต่อ | อ้างอิง</p>
                                <div style="display: flex; justify-content: space-around; flex-wrap: wrap; font-size: 0.95em; padding: 5px 0;">
                                    <span><span style="color:var(--color-primary);">&#9742;</span> **โทร:** ${data.phone}</span>
                                    <span><span style="color:var(--color-primary);">&#9993;</span> **อีเมล:** ${data.email}</span>
                                    <span><span style="color:var(--color-primary);">&#128187;</span> **โปรไฟล์:** <a href="${data.portfolio_link}" style="color: var(--color-text);">[Link]</a></span>
                                </div>
                                <p style="font-size: 0.9em; margin-top: 8px;">**บุคคลอ้างอิง:** ${data.reference}</p>
                            </div>
                        </div>
                    `;
                }
            }

            // Event Listeners (Preview and Select)
            previewButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    const templateId = e.target.dataset.template;
                    const cvHtml = generateCVHtml(templateId, data);
                    cvPreviewArea.innerHTML = cvHtml;
                    modal.style.display = 'block';
                });
            });

            selectButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    const templateId = e.target.dataset.template;
                    alert(`คุณเลือกเทมเพลต: ${templateId}\n(ระบบจะทำการสร้างไฟล์ CV ขนาด A4 ที่มีข้อมูลของคุณ)`);
                });
            });

            closeBtn.addEventListener('click', () => { modal.style.display = 'none'; });

            window.addEventListener('click', (e) => {
                if (e.target === modal) { modal.style.display = 'none'; }
            });
        });
    </script>
</body>
</html>