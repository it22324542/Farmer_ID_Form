<?php include 'includes/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="si">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ගොවි හැඳුනුම්පත් අයදුම්පත</title>
    <link rel="stylesheet" href="assets/Farmer_id_form.css">
</head>
<body>
    <div class="container">
        <form id="farmerForm" action="controllers/process_farmer_registration.php" method="POST" enctype="multipart/form-data">
            <!-- Page Title -->
            <u class="title-section">
                <h1 class="title">ගොවි හැඳුනුම්පත</h1>
            </u>

            <!-- Office Use Section (1st Div) -->
            <div class="office-section">
                <div class="office-header">කාර්යාල ප්‍රයෝජනය සඳහා</div>
                
                <div class="form-group">
                    <label for="farmer-id">ගොවි හැඳුනුම්පතේ අංකය :</label>
                    <input type="text" id="farmer-id" name="farmer_id" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                    <small style="color: #666; font-size: 12px;">සේවා මධ්‍යස්ථානය තෝරන්න පසු ස්වයංක්‍රීයව උත්පාදනය වේ</small>
                </div>

                <div class="form-group">
                    <label for="service-center">අයදුම්පත භාරගන්නා ගොවිජන සේවා මධ්‍යස්ථානය :</label>
                    <select id="service-center" name="asc_center">
                        <option value="">Select Service Center</option>
                        <?php
                        $asc_query = "SELECT asc_id, asc_name FROM tbl_asc";
                        $asc_result = $conn->query($asc_query);
                        while ($asc_row = $asc_result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($asc_row['asc_id']) . "'>" . htmlspecialchars($asc_row['asc_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id-photo">හැඳුනුම්පතේ ඡායාරූපය :</label>
                    <input type="file" id="id-photo" name="id-photo" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="signature">අත්සන :</label>
                    <input type="file" id="signature" name="signature" accept="image/*">
                </div>
            </div>

            <!-- Location Information Section (2nd Div) -->
            <div class="location-section">
                <div class="form-group">
                    <label for="district">දිස්ත්‍රික්කය :</label>
                    <select id="district" name="district" onchange="loadServiceCenters()">
                        <option value="">Select District</option>
                        <?php
                        $district_query = "SELECT dis_id, dis_sname FROM tbl_district";
                        $district_result = $conn->query($district_query);
                        while ($district_row = $district_result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($district_row['dis_id']) . "'>" 
                                . htmlspecialchars($district_row['dis_sname']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="service-center-name">ගොවිජනසේවා සේවා මධ්‍යස්ථානයේ නම :</label>
                    <select id="service-center-name" name="asc_name" 
                            style="color: black; background-color: white;">
                        <option value="">
                            Select Service Center Name
                        </option>
                        <?php
                        $asc_name_query = "SELECT asc_id, asc_name FROM tbl_asc";
                        $asc_name_result = $conn->query($asc_name_query);
                        while ($asc_name_row = $asc_name_result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($asc_name_row['asc_id']) . "'>" 
                                   . htmlspecialchars($asc_name_row['asc_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="registered-org">ලියාපදිංචි ගොවි සංවිධානයේ නම :</label>
                    <input type="text" id="registered-org" name="registered-org">
                </div>
            </div>

            <!-- Personal Information Section (3rd Div) -->
            <div class="personal-info-section">
                <div class="form-group">
                    <label for="full-name-english">සම්පූර්ණ නම (ඉංග්‍රීසියෙන් කැපිටල්) :</label>
                    <input type="text" id="full-name-english" name="full-name-english" style="text-transform: uppercase;">
                </div>

                <div class="form-group">
                    <label for="full-name-sinhala">සම්පූර්ණ නම (සිංහලෙන්) :</label>
                    <input type="text" id="full-name-sinhala" name="full-name-sinhala">
                </div>

                <div class="form-group">
                    <label for="name-with-initials">මුලකුරු සමඟ නම :</label>
                    <input type="text" id="name-with-initials" name="name-with-initials">
                </div>

                <div class="form-group">
                    <label for="birth-date">උපන්දිනය :</label>
                    <input type="date" id="birth-date" name="birth-date">
                </div>

                <div class="form-group">
                    <label>ස්ත්‍රී / පුරුෂ භාවය :</label>
                    <div class="radio-group">
                        <input type="radio" id="female" name="gender" value="female">
                        <label for="female" class="radio-label">ස්ත්‍රී</label>
                        <input type="radio" id="male" name="gender" value="male">
                        <label for="male" class="radio-label">පුරුෂ</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nic-number">ජාතික හැඳුනුම්පත් අංකය :</label>
                    <input type="text" id="nic-number" name="nic-number" placeholder="පැරණි: 123456789V හෝ නව: 199912345678">
                    <div class="error-message" id="nic-error"></div>
                    <div class="success-message" id="nic-success">✓ වලංගු ජාතික හැඳුනුම්පත් අංකයකි</div>
                </div>

                <div class="form-group">
                    <label for="mobile-number">ජංගම දුරකථන අංකය :</label>
                    <input type="tel" id="mobile-number" name="mobile-number">
                </div>

                <div class="form-group">
                    <label for="land-phone">දුරකථන අංකය (Land) :</label>
                    <input type="tel" id="land-phone" name="land-phone">
                </div>

                <div class="form-group">
                    <label for="permanent-address">ස්ථීර ලිපිනය (සිංහලෙන්) :</label>
                    <textarea id="permanent-address" name="permanent-address" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="grama-niladhari">ස්ථීර ග්‍රාමනිලධාරී වසම :</label>
                    <select id="grama-niladhari" name="gn_devision">
                        <option value="">Select Grama Niladhari</option>
                        <?php
                        $gnd_query = "SELECT gnd_id, gnd_sname FROM tbl_gnd";
                        $gnd_result = $conn->query($gnd_query);
                        while ($gnd_row = $gnd_result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($gnd_row['gnd_id']) . "'>" . htmlspecialchars($gnd_row['gnd_sname']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="agrarian-service">කෘ.ප.නි.ස වසම :</label>
                    <input type="text" id="agrarian-service" name="agrarian-service">
                </div>

                <div class="form-group">
                    <label for="temporary-address">තාවකාලික ලිපිනය :</label>
                    <textarea id="temporary-address" name="temporary-address" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>ගොවිතැනේ ස්වභාවය :</label>
                    <div class="checkbox-group">
                        <input type="radio" id="full-time" name="farming-nature" value="full-time">
                        <label for="full-time" class="checkbox-label">පූර්ණ කාලීන</label>
                        
                        <input type="radio" id="part-time" name="farming-nature" value="part-time">
                        <label for="part-time" class="checkbox-label">අර්ධ කාලීන</label>
                        
                        <input type="radio" id="home-garden" name="farming-nature" value="home-garden">
                        <label for="home-garden" class="checkbox-label">ගෙවත්ත</label>
                        
                        <input type="radio" id="rental" name="farming-nature" value="rental">
                        <label for="rental" class="checkbox-label">කුලී</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>ගොවි විශ්‍රාම වැටුපට දායක වී ඇද්ද ?</label>
                    <div class="radio-group">
                        <input type="radio" id="pension-yes" name="pension-contributor" value="yes">
                        <label for="pension-yes" class="radio-label">ඇත</label>
                        <input type="radio" id="pension-no" name="pension-contributor" value="no">
                        <label for="pension-no" class="radio-label">නැත</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>ඉඩමේ විස්තර හා ප්‍රමාණය :</label>
                    <div class="radio-group">
                        <input type="radio" id="land-dry" name="land-type" value="dry" onchange="toggleLandMeasurement()">
                        <label for="land-dry" class="radio-label">ගොඩ</label>
                        <input type="radio" id="land-wet" name="land-type" value="wet" onchange="toggleLandMeasurement()">
                        <label for="land-wet" class="radio-label">මඩ</label>
                    </div>
                    
                    <div id="land-measurement" class="land-measurement" style="display: none;">
                        <div class="measurement-row">
                            <div class="measurement-item">
                                <label for="acres">අක්කර :</label>
                                <input type="number" id="acres" name="acres" min="0" step="1">
                            </div>
                            <div class="measurement-item">
                                <label for="roods">රූඩ් :</label>
                                <input type="number" id="roods" name="roods" min="0" step="1">
                            </div>
                            <div class="measurement-item">
                                <label for="perches">පර්චස් :</label>
                                <input type="number" id="perches" name="perches" min="0" step="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>ඉඩමේ හිමිකාරිත්වය :</label>
                    <div class="checkbox-group">
                        <input type="radio" id="land-owner" name="ownership-of-the-land" value="land-owner">
                        <label for="land-owner" class="checkbox-label">ඉඩම් හිමි</label>
                        
                        <input type="radio" id="tenant" name="ownership-of-the-land" value="tenant">
                        <label for="tenant" class="checkbox-label">අඳ</label>
                        
                        <input type="radio" id="landowner-farmer" name="ownership-of-the-land" value="landowner-farmer">
                        <label for="landowner-farmer" class="checkbox-label">ඉඩම් හිමි ගොවි</label>
                        
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="submit-section">
                <button type="submit" class="submit-btn">අයදුම්පත ඉදිරිපත් කරන්න</button>
            </div>
        </form>
    </div>

<script>
        // Add validation error styling
        const style = document.createElement('style');
        style.textContent = `
            .validation-error {
                color: #d32f2f;
                font-size: 12px;
                margin-top: 4px;
                display: none;
            }
            .validation-error.show {
                display: block;
            }
            .validation-success {
                color: #388e3c;
                font-size: 12px;
                margin-top: 4px;
                display: none;
            }
            .validation-success.show {
                display: block;
            }
            input.error, textarea.error {
                border-color: #d32f2f !important;
                background-color: #ffebee;
            }
            input.success, textarea.success {
                border-color: #388e3c !important;
                background-color: #e8f5e9;
            }
        `;
        document.head.appendChild(style);

        // Validation Functions
        const validators = {
            // 1. Registered Organization - Only English letters and spaces
            registeredOrg: (value) => {
                const pattern = /^[A-Za-z\s]*$/;
                if (!pattern.test(value)) {
                    return { isValid: false, message: "ඉංග්‍රීසි අකුරු සහ හිස්තැන් පමණක් ඇතුළත් කරන්න" };
                }
                return { isValid: true, message: "" };
            },

            // 2. Full Name English - Only capital English letters and spaces
            fullNameEnglish: (value) => {
                const pattern = /^[A-Z\s]*$/;
                if (!pattern.test(value)) {
                    return { isValid: false, message: "කැපිටල් ඉංග්‍රීසි අකුරු සහ හිස්තැන් පමණක් ඇතුළත් කරන්න" };
                }
                return { isValid: true, message: "" };
            },

            // 3. Full Name Sinhala - Only Sinhala letters and spaces
            fullNameSinhala: (value) => {
                const pattern = /^[\u0D80-\u0DFF\s]*$/;
                if (!pattern.test(value)) {
                    return { isValid: false, message: "සිංහල අකුරු සහ හිස්තැන් පමණක් ඇතුළත් කරන්න" };
                }
                return { isValid: true, message: "" };
            },

            // 4. Name with Initials - Only English letters, dots and spaces
            nameWithInitials: (value) => {
                const pattern = /^[A-Za-z.\s]*$/;
                if (!pattern.test(value)) {
                    return { isValid: false, message: "ඉංග්‍රීසි අකුරු, '.' සහ හිස්තැන් පමණක් ඇතුළත් කරන්න" };
                }
                return { isValid: true, message: "" };
            },

            // 5. Mobile Number - Numbers with optional + at start, max 10 or 12 digits
            mobileNumber: (value) => {
                const withPlusPattern = /^\+\d{11}$/;
                const withoutPlusPattern = /^\d{10}$/;
                
                if (value.startsWith('+')) {
                    if (!withPlusPattern.test(value)) {
                        return { isValid: false, message: "+ සමඟ අංක 12ක් තිබිය යුතුය (උදා: +94711234567)" };
                    }
                } else {
                    if (!withoutPlusPattern.test(value)) {
                        return { isValid: false, message: "අංක 10ක් තිබිය යුතුය (උදා: 0711234567)" };
                    }
                }
                return { isValid: true, message: "✓ වලංගු ජංගම දුරකථන අංකයකි" };
            },

            // 6. Land Phone - Same as mobile number
            landPhone: (value) => {
                const withPlusPattern = /^\+\d{11}$/;
                const withoutPlusPattern = /^\d{10}$/;
                
                if (value.startsWith('+')) {
                    if (!withPlusPattern.test(value)) {
                        return { isValid: false, message: "+ සමඟ අංක 12ක් තිබිය යුතුය (උදා: +94112345678)" };
                    }
                } else {
                    if (!withoutPlusPattern.test(value)) {
                        return { isValid: false, message: "අංක 10ක් තිබිය යුතුය (උදා: 0112345678)" };
                    }
                }
                return { isValid: true, message: "✓ වලංගු දුරකථන අංකයකි" };
            },

            // 7. Permanent Address - Sinhala letters, numbers and specified symbols
            permanentAddress: (value) => {
                const pattern = /^[\u0D80-\u0DFF0-9,.\s/]*$/;
                if (!pattern.test(value)) {
                    return { isValid: false, message: "සිංහල අකුරු, අංක, ',' '.' '/' සහ හිස්තැන් පමණක් ඇතුළත් කරන්න" };
                }
                return { isValid: true, message: "" };
            },

            // 8. Temporary Address - English letters, numbers and specified symbols
            temporaryAddress: (value) => {
                const pattern = /^[A-Za-z0-9,.\s/]*$/;
                if (!pattern.test(value)) {
                    return { isValid: false, message: "ඉංග්‍රීසි අකුරු, අංක, ',' '.' '/' සහ හිස්තැන් පමණක් ඇතුළත් කරන්න" };
                }
                return { isValid: true, message: "" };
            },

            // 9-11. Land measurements - Only integers
            landMeasurement: (value, fieldName) => {
                const pattern = /^\d+$/;
                if (value && !pattern.test(value)) {
                    return { isValid: false, message: "සම්පූර්ණ අංක පමණක් ඇතුළත් කරන්න" };
                }
                return { isValid: true, message: "" };
            }
        };

        // Helper function to add validation UI elements
        function addValidationElements(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'validation-error';
            errorDiv.id = `${inputId}-error`;
            
            const successDiv = document.createElement('div');
            successDiv.className = 'validation-success';
            successDiv.id = `${inputId}-success`;
            
            input.parentNode.appendChild(errorDiv);
            input.parentNode.appendChild(successDiv);
        }

        // Helper function to show validation result
        function showValidation(inputId, validation) {
            const input = document.getElementById(inputId);
            const errorDiv = document.getElementById(`${inputId}-error`);
            const successDiv = document.getElementById(`${inputId}-success`);
            
            if (!input || !errorDiv || !successDiv) return;
            
            input.classList.remove('error', 'success');
            errorDiv.classList.remove('show');
            successDiv.classList.remove('show');
            
            if (input.value.length === 0) return;
            
            if (validation.isValid) {
                input.classList.add('success');
                if (validation.message) {
                    successDiv.textContent = validation.message;
                    successDiv.classList.add('show');
                }
                errorDiv.textContent = '';
            } else {
                input.classList.add('error');
                errorDiv.textContent = validation.message;
                errorDiv.classList.add('show');
            }
        }

        // NIC Validation Function
        function validateNIC(str) {
            str = str.replace(/\s+/g, '').toUpperCase();
            var lastCharOfid = str.slice(-1);
            let firsttwoid = str.substring(0, 2);
            let firtsninid = str.substring(0, 9);
            if (str.length != 10 && str.length != 12) {
                return { isValid: false, message: "කරුණාකර වලංගු NIC අංකයක් ඇතුළත් කරන්න (ඉලක්කම් 10 හෝ 12)" };
            } 
            else if (str.length == 10) {
                if (isNaN(firtsninid)) {
                    return { isValid: false, message: "කරුණාකර වලංගු NIC අංකයක් ඇතුළත් කරන්න (පැරණි ආකෘතිය: ඉලක්කම් 9 සහ V හෝ X)" };
                } else if (lastCharOfid != "V" && lastCharOfid != "X") {
                    return { isValid: false, message: "කරුණාකර වලංගු NIC අංකයක් ඇතුළත් කරන්න (පැරණි ආක�ৃතිය අවසානයේ V හෝ X)" };
                } 
            } 
            else if (str.length == 12) {
                if (isNaN(str)) {
                    return { isValid: false, message: "කරුණාකර වලංගු NIC අංකයක් ඇතුළත් කරන්න (නව ආකෘතිය: ඉලක්කම් 12)" };
                } else if (firsttwoid != "19" && firsttwoid != "20") {
                    return { isValid: false, message: "කරුණාකර වලංගු NIC අංකයක් ඇතුළත් කරන්න (නව ආකෘතිය 19 හෝ 20 ආරම්භය)" };
                } 
            }
            return { isValid: true, message: "✓ වලංගු ජාතික හැඳුනුම්පත් අංකයකි" };
        }

        // Land measurement toggle function
        function toggleLandMeasurement() {
            const landMeasurement = document.getElementById('land-measurement');
            const dryRadio = document.getElementById('land-dry');
            const wetRadio = document.getElementById('land-wet');
            
            if (dryRadio.checked || wetRadio.checked) {
                landMeasurement.style.display = 'block';
            } else {
                landMeasurement.style.display = 'none';
            }
        }

        // Initialize validation elements for all fields
        const fieldsToValidate = [
            'registered-org',
            'full-name-english',
            'full-name-sinhala',
            'name-with-initials',
            'mobile-number',
            'land-phone',
            'permanent-address',
            'temporary-address',
            'acres',
            'roods',
            'perches',
            'nic-number'
        ];

        fieldsToValidate.forEach(addValidationElements);

        // 1. Registered Organization Validation
        document.getElementById('registered-org').addEventListener('input', function(e) {
            // Prevent non-English letters and spaces
            this.value = this.value.replace(/[^A-Za-z\s]/g, '');
            const validation = validators.registeredOrg(this.value);
            showValidation('registered-org', validation);
        });

        // 2. Full Name English Validation
        document.getElementById('full-name-english').addEventListener('input', function(e) {
            // Auto-capitalize and prevent non-capital letters
            this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '');
            const validation = validators.fullNameEnglish(this.value);
            showValidation('full-name-english', validation);
        });

        // 3. Full Name Sinhala Validation
        document.getElementById('full-name-sinhala').addEventListener('input', function(e) {
            const validation = validators.fullNameSinhala(this.value);
            showValidation('full-name-sinhala', validation);
            // Remove non-Sinhala characters
            if (!validation.isValid) {
                this.value = this.value.replace(/[^\u0D80-\u0DFF\s]/g, '');
            }
        });

        // 4. Name with Initials Validation
        document.getElementById('name-with-initials').addEventListener('input', function(e) {
            // Prevent invalid characters
            this.value = this.value.replace(/[^A-Za-z.\s]/g, '');
            const validation = validators.nameWithInitials(this.value);
            showValidation('name-with-initials', validation);
        });

        // 5. Mobile Number Validation
        document.getElementById('mobile-number').addEventListener('input', function(e) {
            let value = this.value;
            
            // Allow only + at the beginning and numbers
            if (value.startsWith('+')) {
                value = '+' + value.substring(1).replace(/[^\d]/g, '');
                // Limit to +94xxxxxxxxx (12 characters total)
                if (value.length > 12) {
                    value = value.substring(0, 12);
                }
            } else {
                value = value.replace(/[^\d]/g, '');
                // Limit to 10 digits
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
            }
            
            this.value = value;
            const validation = validators.mobileNumber(value);
            showValidation('mobile-number', validation);
        });

        // 6. Land Phone Validation
        document.getElementById('land-phone').addEventListener('input', function(e) {
            let value = this.value;
            
            // Allow only + at the beginning and numbers
            if (value.startsWith('+')) {
                value = '+' + value.substring(1).replace(/[^\d]/g, '');
                // Limit to +94xxxxxxxxx (12 characters total)
                if (value.length > 12) {
                    value = value.substring(0, 12);
                }
            } else {
                value = value.replace(/[^\d]/g, '');
                // Limit to 10 digits
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
            }
            
            this.value = value;
            const validation = validators.landPhone(value);
            showValidation('land-phone', validation);
        });

        // 7. Permanent Address Validation
        document.getElementById('permanent-address').addEventListener('input', function(e) {
            const validation = validators.permanentAddress(this.value);
            showValidation('permanent-address', validation);
            // Remove invalid characters
            if (!validation.isValid) {
                this.value = this.value.replace(/[^\u0D80-\u0DFF0-9,.\s/]/g, '');
            }
        });

        // 8. Temporary Address Validation
        document.getElementById('temporary-address').addEventListener('input', function(e) {
            // Prevent invalid characters
            this.value = this.value.replace(/[^A-Za-z0-9,.\s/]/g, '');
            const validation = validators.temporaryAddress(this.value);
            showValidation('temporary-address', validation);
        });

        // 9-11. Land Measurement Validations
        ['acres', 'roods', 'perches'].forEach(fieldId => {
            document.getElementById(fieldId).addEventListener('input', function(e) {
                // Allow only integers
                this.value = this.value.replace(/[^\d]/g, '');
                const validation = validators.landMeasurement(this.value, fieldId);
                showValidation(fieldId, validation);
            });
        });

        // Real-time NIC validation
        document.getElementById('nic-number').addEventListener('input', function() {
            let value = this.value.replace(/\s+/g, '').toUpperCase();
            // Restrict input to numbers and V/X for old format
            if (value.length <= 10) {
                value = value.replace(/[^0-9VX]/g, '');
            } else {
                value = value.replace(/[^0-9]/g, '');
            }
            this.value = value;
            const validation = validateNIC(value);
            showValidation('nic-number', validation);
        });

        // Form submission validation
        document.getElementById('farmerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isFormValid = true;
            const validationErrors = [];
            
            // Ensure asc_center value is set before validation
            const serviceCenterSelect = document.getElementById('service-center');
            if (serviceCenterSelect && selectedAscId) {
                serviceCenterSelect.value = selectedAscId;
            }
            
            // Debug: Check form values before submission
            console.log('ASC Center on submit:', serviceCenterSelect.value);
            console.log('Selected ASC ID:', selectedAscId);
            
            // Validate farmer ID was generated (this confirms service center was selected)
            const farmerIdElement = document.getElementById('farmer-id');
            const farmerId = farmerIdElement ? farmerIdElement.value : '';
            
            if (!farmerId || farmerId === '' || farmerId === 'Loading...') {
                validationErrors.push('කරුණාකර ගොවිජන සේවා මධ්‍යස්ථානය තෝරා ගොවි හැඳුනුම්පත් අංකය උත්පාදනය කරන්න');
                isFormValid = false;
            }
            
            // Validate required text fields
            const fullNameEnglish = document.getElementById('full-name-english').value;
            if (!fullNameEnglish || fullNameEnglish.trim() === '') {
                validationErrors.push('සම්පූර්ණ නම (ඉංග්‍රීසියෙන්) අවශ්‍යයි');
                isFormValid = false;
            }
            
            const fullNameSinhala = document.getElementById('full-name-sinhala').value;
            if (!fullNameSinhala || fullNameSinhala.trim() === '') {
                validationErrors.push('සම්පූර්ණ නම (සිංහලෙන්) අවශ්‍යයි');
                isFormValid = false;
            }
            
            const birthDate = document.getElementById('birth-date').value;
            if (!birthDate || birthDate === '') {
                validationErrors.push('උපන්දිනය අවශ්‍යයි');
                isFormValid = false;
            }
            
            const nicNumber = document.getElementById('nic-number').value;
            if (!nicNumber || nicNumber.trim() === '') {
                validationErrors.push('ජාතික හැඳුනුම්පත් අංකය අවශ්‍යයි');
                isFormValid = false;
            } else {
                const nicValidation = validateNIC(nicNumber);
                if (!nicValidation.isValid) {
                    validationErrors.push('ජාතික හැඳුනුම්පත් අංකය වලංගු නොවේ');
                    isFormValid = false;
                }
            }
            
            const mobileNumber = document.getElementById('mobile-number').value;
            if (!mobileNumber || mobileNumber.trim() === '') {
                validationErrors.push('ජංගම දුරකථන අංකය අවශ්‍යයි');
                isFormValid = false;
            }
            
            const permanentAddress = document.getElementById('permanent-address').value;
            if (!permanentAddress || permanentAddress.trim() === '') {
                validationErrors.push('ස්ථීර ලිපිනය අවශ්‍යයි');
                isFormValid = false;
            }
            
            // Validate required radio buttons
            const gender = document.querySelector('input[name="gender"]:checked');
            if (!gender) {
                validationErrors.push('ස්ත්‍රී / පුරුෂ භාවය තෝරන්න');
                isFormValid = false;
            }
            
            const farmingNature = document.querySelector('input[name="farming-nature"]:checked');
            if (!farmingNature) {
                validationErrors.push('ගොවිතැනේ ස්වභාවය තෝරන්න');
                isFormValid = false;
            }
            
            const pensionContributor = document.querySelector('input[name="pension-contributor"]:checked');
            if (!pensionContributor) {
                validationErrors.push('ගොවි විශ්‍රාම වැටුප් දායකත්වය තෝරන්න');
                isFormValid = false;
            }
            
            const landType = document.querySelector('input[name="land-type"]:checked');
            if (!landType) {
                validationErrors.push('ඉඩමේ වර්ගය (ගොඩ/මඩ) තෝරන්න');
                isFormValid = false;
            }
            
            const landOwnership = document.querySelector('input[name="ownership-of-the-land"]:checked');
            if (!landOwnership) {
                validationErrors.push('ඉඩමේ හිමිකාරිත්වය තෝරන්න');
                isFormValid = false;
            }
            
            // Validate all fields
            const registeredOrg = document.getElementById('registered-org').value;
            if (registeredOrg && !validators.registeredOrg(registeredOrg).isValid) {
                validationErrors.push('ලියාපදිංචි ගොවි සංවිධානයේ නම වලංගු නොවේ');
                isFormValid = false;
            }
            
            // Additional format validations for filled fields
            if (mobileNumber && !validators.mobileNumber(mobileNumber).isValid) {
                validationErrors.push('ජංගම දුරකථන අංකය වලංගු නොවේ');
                isFormValid = false;
            }
            
            const landPhone = document.getElementById('land-phone').value;
            if (landPhone && !validators.landPhone(landPhone).isValid) {
                validationErrors.push('දුරකථන අංකය වලංගු නොවේ');
                isFormValid = false;
            }
            
            if (!isFormValid) {
                alert('කරුණාකර පහත දෝෂ නිවැරදි කරන්න:\n\n' + validationErrors.join('\n'));
                return;
            }
            
            // Submit the form
            this.submit();
        });






        // Store selected ASC ID globally
        let selectedAscId = '';
        
        // Auto-generate Farmer ID when service center is selected
        document.getElementById('service-center').addEventListener('change', function() {
            const ascId = this.value;
            selectedAscId = ascId; // Store for form submission
            const farmerIdInput = document.getElementById('farmer-id');
            
            if (ascId) {
                // Show loading state
                farmerIdInput.value = 'Loading...';
                farmerIdInput.style.backgroundColor = '#fff3cd';
                
                // Create FormData
                const formData = new FormData();
                formData.append('asc_id', ascId);
                
                // Send AJAX request
                fetch('controllers/generate_farmer_id.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        farmerIdInput.value = data.farmer_id;
                        farmerIdInput.style.backgroundColor = '#d4edda'; // Green background
                        
                        // Optional: Log details for debugging
                        console.log('Generated Farmer ID:', data.farmer_id);
                        console.log('Original ASC Code:', data.asc_code);
                        console.log('Formatted Code:', data.formatted_code);
                        console.log('Sequence Number:', data.sequence);
                    } else {
                        farmerIdInput.value = '';
                        farmerIdInput.style.backgroundColor = '#f8d7da'; // Red background
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    farmerIdInput.value = '';
                    farmerIdInput.style.backgroundColor = '#f8d7da';
                    alert('Failed to generate Farmer ID. Please try again.');
                });
            } else {
                farmerIdInput.value = '';
                farmerIdInput.style.backgroundColor = '#f0f0f0';
            }
        });


        // Dynamic loading of service centers based on district
        function loadServiceCenters() {
            const districtSelect = document.getElementById('district');
            const serviceCenterSelect = document.getElementById('service-center');
            const districtValue = districtSelect.value;

            serviceCenterSelect.innerHTML = '<option value="">Select Service Center</option>';
            // Note: The PHP code for loading service centers needs to be implemented server-side
        }
</script>
</body>
</html>


