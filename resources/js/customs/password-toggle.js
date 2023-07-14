const passwordToggle = {
    init : function(id){
        var inputGroup = document.getElementById(id);
        var ShowPasswordToggle = inputGroup.querySelector("[type='password']");
        ShowPasswordToggle.onclick = function () {
            inputGroup.querySelector("[type='password']").classList.add("input-password");
            inputGroup.querySelector(".toggle-password").classList.remove("d-none");

            const passwordInput = inputGroup.querySelector("[type='password']");
            const togglePasswordButton = inputGroup.querySelector(".toggle-password");

            togglePasswordButton.addEventListener("click", function(){
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    togglePasswordButton.setAttribute("aria-label", "Hide password.");
                } else {
                    passwordInput.type = "password";
                    togglePasswordButton.setAttribute("aria-label","Show password as plain text. "+"Warning: this will display your password on the screen.");
                }
            });
        };
    }
}

export {passwordToggle}