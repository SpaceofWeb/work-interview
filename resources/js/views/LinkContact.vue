<template>
    <div>
        <h1>Добавление контакта</h1>
        <div v-if="error" class="alert alert-danger" role="alert">
            <strong>Ошибка!</strong> {{ error }}
        </div>
        <form novalidate>
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <label for="name">Имя</label>
                    <input id="name" v-model:required.trim="form.name" class="form-control" required type="text">
                    <div v-if="validation?.['name']?.[0]" class="invalid-feedback d-inline">
                        {{ validation?.['name']?.[0] }}
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <label for="phone">Телефон</label>
                    <input id="phone" v-model:required.trim="form.phone" class="form-control" required type="tel">
                    <div v-if="validation?.['phone']?.[0]" class="invalid-feedback d-inline">
                        {{ validation?.['phone']?.[0] }}
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <label for="comment">Комментарий</label>
                    <textarea id="comment" v-model:required.trim="form.comment" class="form-control" required
                              rows="2"></textarea>
                    <div v-if="validation?.['comment']?.[0]" class="invalid-feedback d-inline">
                        {{ validation?.['comment']?.[0] }}
                    </div>
                </div>
            </div>
            <button :disabled="!loaded" class="btn btn-primary mt-2" type="submit" v-on:click.prevent="sendData">
                Привязать
            </button>
        </form>
    </div>
</template>

<script>
export default {
    name: 'LinkContact',
    props: ['id'],
    data() {
        return {
            error: null,
            loaded: true,
            validation: [],
            form: {
                name: 'name',
                phone: 'phone',
                comment: 'comment',
            },
        }
    },
    methods: {
        sendData() {
            this.loaded = false

            const data = {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(this.form)
            }

            fetch(`/api/leads/${this.id}/contact`, data)
                .then(response => response.json())
                .then(data => {
                    if (!data.hasOwnProperty('error')) {
                        this.$router.push('/')

                    } else {
                        this.error = data.detail

                        if (data.hasOwnProperty('data')) {
                            this.validation = data.data
                        }
                    }
                    this.loaded = true
                })
                .catch(error => {
                    console.error(error)
                    this.error = error
                    this.loaded = true
                });
        },
    },
}
</script>

<style scoped>

</style>
