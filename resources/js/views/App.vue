<template>
    <div>
        <div class="container">
            <nav class="navbar navbar-expand-sm navbar-light bg-light">
                <router-link class="navbar-brand" to="/">Главная</router-link>
                <button aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
                        class="navbar-toggler" data-target="#navbarSupportedContent"
                        data-toggle="collapse" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div id="navbarSupportedContent" class="collapse navbar-collapse">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <router-link class="nav-link" to="/history">История</router-link>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="container mt-3">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
export default {}
</script>

<style>

</style>
