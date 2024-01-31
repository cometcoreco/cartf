<script>
    function generateRandomPassword() {
        const minLength = 8;
        const maxLength = 12;
        const stringCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_-+=<>?/[]{}|';

        const passwordLength = Math.floor(Math.random() * (maxLength - minLength + 1)) + minLength;
        let password = '';

        for (let i = 0; i < passwordLength; i++) {
            const randomIndex = Math.floor(Math.random() * stringCharacters.length);
            password += stringCharacters[randomIndex];
        }

        return password;
    }
</script>
