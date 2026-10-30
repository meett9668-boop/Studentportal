/**
 * Student Hub - Unified Client-Side JavaScript
 * Consolidating DOM controls, theme toggle, and client-side form validation.
 */
"use strict";

document.addEventListener("DOMContentLoaded", function () {
    // ----------------------------------------------------
    // 1. Dark/Light Theme Switching (Practical 4)
    // ----------------------------------------------------
    const themeToggleBtn = document.getElementById("themeToggleBtn");
    const currentTheme = localStorage.getItem("studenthub_theme") || "light";

    if (currentTheme === "dark") {
        document.body.classList.add("dark");
        if (themeToggleBtn) {
            themeToggleBtn.innerHTML = '<i class="bi bi-sun-fill text-warning"></i>';
        }
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener("click", function () {
            document.body.classList.toggle("dark");
            const isDark = document.body.classList.contains("dark");
            localStorage.setItem("studenthub_theme", isDark ? "dark" : "light");
            themeToggleBtn.innerHTML = isDark
                ? '<i class="bi bi-sun-fill text-warning"></i>'
                : '<i class="bi bi-moon-stars-fill"></i>';
        });
    }

    // ----------------------------------------------------
    // 2. Client-side Real-time Registration Validation (Practical 5)
    // ----------------------------------------------------
    const regForm = document.getElementById("studentRegisterForm");
    if (regForm) {
        const passwordInput = document.getElementById("reg_password");
        const confirmInput = document.getElementById("reg_confirm_password");
        const strengthBar = document.getElementById("regStrengthBar");
        const strengthText = document.getElementById("regStrengthText");

        if (passwordInput && strengthBar) {
            passwordInput.addEventListener("input", function () {
                const val = passwordInput.value;
                let score = 0;
                if (val.length >= 8) score++;
                if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
                if (/\d/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;

                strengthBar.className = "strength-bar";
                if (val.length === 0) {
                    strengthBar.style.width = "0%";
                    if (strengthText) strengthText.textContent = "";
                } else if (score <= 1) {
                    strengthBar.classList.add("strength-level-1");
                    if (strengthText) strengthText.textContent = "Weak (Minimum 8 chars required)";
                } else if (score === 2) {
                    strengthBar.classList.add("strength-level-2");
                    if (strengthText) strengthText.textContent = "Fair";
                } else if (score === 3) {
                    strengthBar.classList.add("strength-level-3");
                    if (strengthText) strengthText.textContent = "Good";
                } else {
                    strengthBar.classList.add("strength-level-4");
                    if (strengthText) strengthText.textContent = "Strong password";
                }
            });
        }

        regForm.addEventListener("submit", function (e) {
            if (passwordInput && confirmInput && passwordInput.value !== confirmInput.value) {
                e.preventDefault();
                alert("Passwords do not match! Please check and re-enter.");
                confirmInput.focus();
            }
        });
    }

    // ----------------------------------------------------
    // 3. Auto-dismiss alerts after 5 seconds
    // ----------------------------------------------------
    const autoAlerts = document.querySelectorAll(".alert-dismissible");
    autoAlerts.forEach(function (alert) {
        setTimeout(function () {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) bsAlert.close();
            }
        }, 6000);
    });
});

