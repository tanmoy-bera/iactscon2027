/**
 * IACTSCON 2027 — Art Exhibition Submission Form
 * Handles: category single-select, per-step validation,
 * review-step population, and AJAX submission.
 */

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("userAccountSetupForm");

    /* ---------------------------------------------------------------
       1. Category checkboxes behave like a single-select (radio)
       --------------------------------------------------------------- */
    const categoryCheckboxes = document.querySelectorAll('input[name="regimood"]');
    categoryCheckboxes.forEach((box) => {
        box.addEventListener("change", () => {
            if (box.checked) {
                categoryCheckboxes.forEach((other) => {
                    if (other !== box) other.checked = false;
                });
            }
        });
    });

    /* ---------------------------------------------------------------
       1b. Delegate lookup by email (auto-fill Conference Reg. No.)
       This is UX-only — the server independently re-checks the email
       and ignores whatever delegate_id/medical_reg_no the client
       posts, so nothing here needs to be trusted.
       --------------------------------------------------------------- */
    const emailInput      = document.getElementById("email");
    const delegateIdInput = document.getElementById("delegate_id");
    const regNoInput      = document.getElementById("medical_reg_no");
    const lookupHint      = document.getElementById("delegate_lookup_hint");

    const setLookupHint = (text, isFound) => {
        if (!lookupHint) return;
        lookupHint.textContent = text;
        lookupHint.style.color = isFound ? "#1e7e34" : "#6c757d";
    };

    const clearDelegateFields = () => {
        if (delegateIdInput) delegateIdInput.value = "";
        if (regNoInput) regNoInput.value = "";
        setLookupHint("", false);
    };

    const lookupDelegateByEmail = (email) => {
        setLookupHint("Checking…", false);
        fetch("art_exhibition_delegate_lookup.php?email=" + encodeURIComponent(email))
            .then((res) => res.json())
            .then((data) => {
                if (data.status === "found") {
                    if (delegateIdInput) delegateIdInput.value = data.delegate_id;
                    if (regNoInput) regNoInput.value = data.registration_id;
                    setLookupHint("Existing registration found and linked.", true);
                } else {
                    clearDelegateFields();
                    setLookupHint("No existing registration", false);
                }
            })
            .catch(() => {
                clearDelegateFields();
            });
    };

    if (emailInput) {
        emailInput.addEventListener("blur", () => {
            const email = emailInput.value.trim();
            if (email && isValidEmail(email)) {
                lookupDelegateByEmail(email);
            } else {
                clearDelegateFields();
            }
        });
    }

    /* ---------------------------------------------------------------
       1c. Stop Enter from silently submitting the form from any step.
       All four steps share one <form> with only one type="submit"
       button (Step 4's), so pressing Enter in a Step 1/2/3 field
       would otherwise submit immediately — even without a click.
       Instead, Enter advances to that step's "Next" button.
       --------------------------------------------------------------- */
    form.addEventListener("keydown", (e) => {
        if (e.key !== "Enter") return;
        if (e.target.tagName === "TEXTAREA") return; // allow newlines there

        const currentStep = e.target.closest(".upload_form_box");
        if (!currentStep) return;

        // On the final step, let Enter submit normally (falls through
        // to the real submit handler below).
        if (currentStep.id === "step-4") return;

        e.preventDefault();
        const nextBtn = currentStep.querySelector(".btn-navigate-form-step.next");
        if (nextBtn) nextBtn.click();
    });

    /* ---------------------------------------------------------------
       2. Per-step validation before allowing "Next"
       --------------------------------------------------------------- */
    const showFieldError = (input, message) => {
        clearFieldError(input);
        const err = document.createElement("div");
        err.className = "field_error";
        err.style.color = "#c0392b";
        err.style.fontSize = "12px";
        err.style.marginTop = "4px";
        err.textContent = message;
        input.insertAdjacentElement("afterend", err);
        input.classList.add("input-error");
    };

    const clearFieldError = (input) => {
        input.classList.remove("input-error");
        const next = input.nextElementSibling;
        if (next && next.classList.contains("field_error")) next.remove();
    };

    const isValidEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    const isValidMobile = (value) => /^[0-9]{7,15}$/.test(value);
    const isValidCloudLink = (value) => {
        try {
            const url = new URL(value);
            const allowedHosts = [
                "drive.google.com",
                "icloud.com",
                "onedrive.live.com",
                "1drv.ms",
                "dropbox.com",
            ];
            return allowedHosts.some((host) => url.hostname.includes(host));
        } catch (e) {
            return false;
        }
    };

    const validateStep1 = () => {
        let valid = true;
        const firstName = document.getElementById("full_name");
        const email = document.getElementById("email");
        const mobileNo = document.getElementById("mobile_no");

        if (!firstName.value.trim()) {
            showFieldError(firstName, "First name is required.");
            valid = false;
        } else {
            clearFieldError(firstName);
        }

        if (!email.value.trim() || !isValidEmail(email.value.trim())) {
            showFieldError(email, "Enter a valid email address.");
            valid = false;
        } else {
            clearFieldError(email);
        }

        if (!mobileNo.value.trim() || !isValidMobile(mobileNo.value.trim())) {
            showFieldError(mobileNo, "Enter a valid mobile number.");
            valid = false;
        } else {
            clearFieldError(mobileNo);
        }

        return valid;
    };

    const validateStep2 = () => {
        const selected = document.querySelector('input[name="regimood"]:checked');
        const wrap = document.querySelector(".cus_check_wrap.g2");
        let notice = wrap.querySelector(".field_error");
        if (!selected) {
            if (!notice) {
                notice = document.createElement("div");
                notice.className = "field_error";
                notice.style.color = "#c0392b";
                notice.style.marginTop = "10px";
                notice.textContent = "Please select one category to continue.";
                wrap.insertAdjacentElement("afterend", notice);
            }
            return false;
        }
        if (notice) notice.remove();
        return true;
    };

    const validateStep3 = () => {
        let valid = true;
        const link = document.getElementById("gdrive_link");
        const title = document.getElementById("artwork_title");

        if (!link.value.trim() || !isValidCloudLink(link.value.trim())) {
            showFieldError(
                link,
                "Enter a valid Google Drive / iCloud / OneDrive / Dropbox link."
            );
            valid = false;
        } else {
            clearFieldError(link);
        }

        if (!title.value.trim()) {
            showFieldError(title, "Artwork / Photograph title is required.");
            valid = false;
        } else {
            clearFieldError(title);
        }

        return valid;
    };

    /* ---------------------------------------------------------------
       3. Populate the review step (Step 4) from earlier inputs
       --------------------------------------------------------------- */
    const populateReview = () => {
        const val = (id) => {
            const el = document.getElementById(id);
            return el ? el.value.trim() : "";
        };

        document.getElementById("review_name").textContent = val("full_name");
        document.getElementById("review_mobile").textContent =
            val("mobile_isd") + val("mobile_no");
        document.getElementById("review_reg_no").textContent =
            val("medical_reg_no") || "—";
        document.getElementById("review_email").textContent = val("email");

        const selectedCategory = document.querySelector('input[name="regimood"]:checked');
        document.getElementById("review_category").textContent = selectedCategory
            ? selectedCategory.getAttribute("data-category-name")
            : "";

        document.getElementById("review_title").textContent = val("artwork_title");
        document.getElementById("review_notes").textContent = val("medium_notes") || "—";

        const link = val("gdrive_link");
        document.getElementById("review_link").textContent = link;
        const openLink = document.getElementById("review_link_open");
        if (openLink) openLink.setAttribute("href", link);
    };

    /* ---------------------------------------------------------------
       4. Wire validation into the existing next/previous buttons
       --------------------------------------------------------------- */
    document.querySelectorAll(".btn-navigate-form-step.next").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const currentStep = btn.closest(".upload_form_box");
            const currentStepId = currentStep.id;

            let valid = true;
            if (currentStepId === "step-1") valid = validateStep1();
            if (currentStepId === "step-2") valid = validateStep2();
            if (currentStepId === "step-3") valid = validateStep3();

            if (!valid) {
                e.stopImmediatePropagation();
                return;
            }

            if (btn.getAttribute("step_number") === "3") {
                const selectedCategory = document.querySelector('input[name="regimood"]:checked');
                const label = document.getElementById("step3_category_label");
                if (label) {
                    label.textContent = selectedCategory
                        ? selectedCategory.getAttribute("data-category-name")
                        : "";
                }
            }

            if (btn.getAttribute("step_number") === "4") {
                populateReview();
            }
        }, true); // capture phase so this runs before navigateToFormStep
    });

    /* ---------------------------------------------------------------
       6. Step 5 — success screen population + actions
       --------------------------------------------------------------- */
    const populateSuccessStep = (data) => {
        const setText = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value || "";
        };

        setText("success_ref_id", data.reference_id);
        setText("success_submitted_at", data.submitted_at);
        setText("success_contributor_name", data.contributor_name);
        setText("success_contributor_regno", data.contributor_reg_no);
        setText("success_category", data.category);
        setText("success_title", data.title);
        setText("success_cloud_link", data.cloud_link);

        const openLink = document.getElementById("success_open_link");
        if (openLink) openLink.setAttribute("href", data.cloud_link || "#");
    };

    const showSuccessStep = () => {
        document.querySelectorAll(".upload_form_box").forEach((el) => el.classList.add("d-none"));
        const stepFive = document.getElementById("step-5");
        if (stepFive) stepFive.classList.remove("d-none");
        const stepperWrap = document.querySelector(".form-stepper");
        if (stepperWrap) stepperWrap.classList.add("d-none");
    };

    const copyLinkBtn = document.getElementById("success_copy_link");
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener("click", (e) => {
            e.preventDefault();
            const link = document.getElementById("success_cloud_link").textContent;
            if (!link) return;
            navigator.clipboard.writeText(link).then(() => {
                const original = copyLinkBtn.innerHTML;
                copyLinkBtn.innerHTML = 'Copied<i class="fal fa-check"></i>';
                setTimeout(() => {
                    copyLinkBtn.innerHTML = original;
                }, 1500);
            });
        });
    }

    const printBtn = document.getElementById("success_print_btn");
    if (printBtn) {
        printBtn.addEventListener("click", (e) => {
            e.preventDefault();
            document.body.classList.add("printing-slip");
            window.print();
            document.body.classList.remove("printing-slip");
        });
    }

    /* ---------------------------------------------------------------
       7. AJAX submission
       --------------------------------------------------------------- */
    const alertBox = document.getElementById("form_alert");
    const submitBtn = document.getElementById("submit_entry_btn");

    const showAlert = (message, type) => {
        if (!alertBox) return;
        alertBox.textContent = message;
        alertBox.style.padding = "10px 14px";
        alertBox.style.borderRadius = "6px";
        alertBox.style.marginBottom = "14px";
        alertBox.style.color = type === "success" ? "#155724" : "#721c24";
        alertBox.style.background = type === "success" ? "#d4edda" : "#f8d7da";
        alertBox.style.border =
            "1px solid " + (type === "success" ? "#c3e6cb" : "#f5c6cb");
    };

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const confirmBox = document.getElementById("confirm_original");
        if (!confirmBox || !confirmBox.checked) {
            showAlert(
                "Please confirm this is your original work before submitting.",
                "error"
            );
            return;
        }

        const formData = new FormData(form);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = "Submitting…";
        }

        fetch(form.getAttribute("action"), {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.status === "success") {
                    populateSuccessStep(data);
                    showSuccessStep();
                    form.reset();
                } else {
                    showAlert(data.message || "Something went wrong. Please try again.", "error");
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Submit Entry<i class="fal fa-paper-plane"></i>';
                    }
                }
            })
            .catch(() => {
                showAlert("Network error. Please try again.", "error");
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit Entry<i class="fal fa-paper-plane"></i>';
                }
            });
    });
});