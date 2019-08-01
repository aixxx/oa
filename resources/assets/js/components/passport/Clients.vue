<template>
    <div>
        <div class="card card-default mb-3">
            <div class="card-header">
                <h3 class="card-title">我管理的应用</h3>
                <div class="card-options">
                    <a class="btn btn-primary btn-sm" tabindex="-1" @click="showCreateClientForm">
                        创建应用
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Current Clients -->
                <p class="mb-0" v-if="clients.length === 0">
                    You have not created any OAuth clients.
                </p>
                <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap" v-if="clients.length > 0">
                    <thead>
                        <tr>
                            <th>Client ID</th>
                            <th>Name</th>
                            <th>Secret</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="client in clients">
                            <!-- ID -->
                            <td>
                                {{ client.id }}
                            </td>

                            <!-- Name -->
                            <td>
                                {{ client.name }}
                            </td>

                            <!-- Secret -->
                            <td>
                                <code>{{ client.secret }}</code>
                            </td>

                            <!-- Edit Button -->
                            <td>
                                <a class="action-link" tabindex="-1" @click="edit(client)">
                                    <i class="fe fe-edit"></i> 编辑
                                </a>
                            </td>

                            <!-- Delete Button -->
                            <td>
                                <a class="action-link text-danger" @click="destroy(client)">
                                    <i class="fe fe-delete"></i> 删除
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- Create Client Modal -->
        <div class="modal fade" id="modal-create-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            创建应用
                        </h4>
                        <div class="card-options">
                            <button type="button" class="card-options-remove" data-dismiss="modal" aria-hidden="true">
                                <i class="la la-close"></i>
                            </button>
                        </div>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="createForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in createForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Create Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group">
                                <label for="create-client-name" class="col-form-label">系统名称</label>
                                <div class="col-md-12">
                                    <input id="create-client-name" type="text" class="form-control"
                                                                @keyup.enter="store" v-model="createForm.name">
                                    <span class="form-text text-muted">
                                        会显示在用户授权登录的页面
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group">
                                <label for="redirect" class="col-form-label">回调 URL</label>
                                <div class="col-md-12">
                                    <input id="redirect" type="text" class="form-control" name="redirect"
                                                    @keyup.enter="store" v-model="createForm.redirect">
                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary" @click="store">
                            创建
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Client Modal -->
        <div class="modal fade" id="modal-edit-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            编辑应用
                        </h4>
                        <div class="card-options">
                            <button type="button" class="card-options-remove" data-dismiss="modal" aria-hidden="true">
                                <i class="fe fe-x"></i>
                            </button>
                        </div>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="editForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in editForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Edit Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group">
                                <label for="edit-client-name" class="col-form-label">名称</label>

                                <div class="col-md-12">
                                    <input id="edit-client-name" type="text" class="form-control"
                                                                @keyup.enter="update" v-model="editForm.name">
                                    <span class="form-text text-muted">
                                        会显示在用户授权登录的页面
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group">
                                <label for="edit-redirect" class="col-form-label">回调 URL</label>
                                <div class="col-md-12">
                                    <input id="edit-redirect" type="text" class="form-control" name="redirect"
                                                    @keyup.enter="update" v-model="editForm.redirect">
                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary" @click="update">
                            保存更改
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                clients: [],

                createForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                },

                editForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                }
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getClients();

                $('#modal-create-client').on('shown.bs.modal', () => {
                    $('#create-client-name').focus();
                });

                $('#modal-edit-client').on('shown.bs.modal', () => {
                    $('#edit-client-name').focus();
                });
            },

            /**
             * Get all of the OAuth clients for the user.
             */
            getClients() {
                axios.get('/oauth/clients')
                        .then(response => {
                            this.clients = response.data;
                        });
            },

            /**
             * Show the form for creating new clients.
             */
            showCreateClientForm() {
                $('#modal-create-client').modal('show');
            },

            /**
             * Create a new OAuth client for the user.
             */
            store() {
                this.persistClient(
                    'post', '/oauth/clients',
                    this.createForm, '#modal-create-client'
                );
            },

            /**
             * Edit the given client.
             */
            edit(client) {
                this.editForm.id = client.id;
                this.editForm.name = client.name;
                this.editForm.redirect = client.redirect;

                $('#modal-edit-client').modal('show');
            },

            /**
             * Update the client being edited.
             */
            update() {
                this.persistClient(
                    'put', '/oauth/clients/' + this.editForm.id,
                    this.editForm, '#modal-edit-client'
                );
            },

            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.getClients();

                        form.name = '';
                        form.redirect = '';
                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Destroy the given client.
             */
            destroy(client) {
                if (confirm("Are you sure you want to delete")) {
                    axios.delete('/oauth/clients/' + client.id)
                        .then(response => {
                            this.getClients();
                        });
                }
            }
        }
    }
</script>
