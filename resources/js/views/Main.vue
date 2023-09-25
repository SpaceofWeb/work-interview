<template>
    <div>
        <h1>Список лидов</h1>
        <section v-if="error">
            <p>{{ error }}</p>
        </section>

        <section v-else-if="!loaded">
            <p>Загрузка...</p>
        </section>

        <section v-else-if="loaded && leads.length === 0">
            <p>Лиды не найдены</p>
        </section>

        <section v-else>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Название</th>
                    <th scope="col">Дата создания</th>
                    <th scope="col">Есть контакт</th>
                    <th scope="col">Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="lead in leads">
                    <th class="align-middle" scope="row">{{ lead.id }}</th>
                    <td class="align-middle">{{ lead.name }}</td>
                    <td class="align-middle">{{ lead.created_at }}</td>
                    <td class="align-middle text-center">{{ lead.haveContacts ? '&#10003;' : '&#x2715;' }}</td>
                    <td class="align-middle text-center">
                        <a
                            v-if="lead.haveContacts"
                            aria-disabled="true"
                            class="btn btn-outline-secondary disabled"
                            role="button"
                            tabindex="-1">
                            Привязать контакт
                        </a>
                        <router-link v-else :to="'/link-contact/'+lead.id" class="btn btn-outline-secondary"
                                     role="button">
                            Привязать контакт
                        </router-link>
                    </td>
                </tr>
                </tbody>
            </table>
        </section>
    </div>
</template>

<script>
export default {
    name: 'Main',
    data() {
        return {
            error: null,
            loaded: false,
            leads: [],
        };
    },
    beforeCreate() {
        this.loaded = false

        fetch('/api/leads')
            .then(response => response.json())
            .then(data => {
                this.leads = data
                this.loaded = true
            })
            .catch(error => console.error(error));
    }
}
</script>

<style scoped>

</style>
