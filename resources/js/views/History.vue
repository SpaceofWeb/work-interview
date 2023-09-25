<template>
    <div>
        <h1>История</h1>
        <section v-if="error">
            <p>{{ error }}</p>
        </section>

        <section v-else-if="!loaded">
            <p>Загрузка...</p>
        </section>

        <section v-else-if="loaded && logs.length === 0">
            <p>Лиды не найдены</p>
        </section>

        <section v-else>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">Дата и время</th>
                    <th scope="col">Действие</th>
                    <th scope="col">Результат</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="log in logs">
                    <th class="align-middle" scope="row">{{ log.created_at | formatDate }}</th>
                    <td class="align-middle">{{ log.action }}</td>
                    <td class="align-middle text-center">{{ log.isDone ? '&#10003;' : '&#x2715;' }}</td>
                </tr>
                </tbody>
            </table>
        </section>
    </div>
</template>

<script>
export default {
    name: 'History',
    data() {
        return {
            error: null,
            loaded: false,
            logs: [],
        };
    },
    beforeCreate() {
        this.loaded = false

        fetch('/api/logs')
            .then(response => response.json())
            .then(data => {
                this.logs = data
                this.loaded = true
            })
            .catch(error => console.error(error));
    },
    filters: {
        formatDate(value) {
            return new Intl.DateTimeFormat('ru-RU', {dateStyle: 'short', timeStyle: 'short'}).format(new Date(value))
        }
    },
}
</script>

<style scoped>

</style>
