<template>
    <div>login</div>
</template>

<script>
    export default {
        name: "Login",

        created() {
            this.$auth.login({

                params: {
                    email: 'serdnaley@gmail.com',
                    password: 'password',
                },

                success: (res) => {

                    console.log('res');

                },

                error: () => {
                    this.error = true;
                    this.errors = ['Invalid login or password.']
                },

                rememberMe: true,
                fetchUser: true
            })
        },
    }
</script>
