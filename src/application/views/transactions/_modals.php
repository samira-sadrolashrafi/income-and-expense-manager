<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
            <!-- Create/edit transaction modal (shared with the dashboard) -->
            <?php
            $this->load->view(
                'transactions/form_modal',
                array('form_prefix' => 'transaction')
            );
            ?>

            <!-- Delete transaction confirmation modal -->
            <div
                class="modal fade tz-modal"
                id="deleteTransactionModal"
                tabindex="-1"
                aria-labelledby="deleteTransactionModalTitle"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h2
                                class="modal-title fs-5 fw-semibold"
                                id="deleteTransactionModalTitle"
                            >
                                <i
                                    class="fa-regular fa-trash-can text-danger me-2"
                                    aria-hidden="true"
                                ></i>
                                حذف تراکنش
                            </h2>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="بستن"
                            ></button>
                        </div>

                        <div class="modal-body">
                            <p class="mb-2">
                                آیا از حذف تراکنش «
                                <span
                                    id="deleteTransactionTitle"
                                    class="fw-semibold"
                                >{{ deleteTarget ? deleteTarget.title : '' }}</span>
                                » اطمینان دارید؟
                            </p>

                            <p class="small text-body-secondary mb-0">
                                تراکنش پس از حذف تا ۳۰ روز در بخش حذف‌شده‌ها قابل بازیابی خواهد بود.
                            </p>

                            <div
                                v-if="deleteMessage.text"
                                id="deleteTransactionMessage"
                                class="alert mt-3 mb-0"
                                :class="'alert-' + deleteMessage.type"
                                :key="deleteMessage.seq"
                                role="alert"
                                aria-live="polite"
                            >{{ deleteMessage.text }}</div>
                        </div>

                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal"
                            >
                                انصراف
                            </button>

                            <button
                                type="button"
                                id="confirmDeleteTransactionButton"
                                class="btn btn-danger"
                                :disabled="deleting"
                                @click="confirmDelete"
                            >
                                <i
                                    class="fa-solid fa-trash-can me-1"
                                    aria-hidden="true"
                                ></i>
                                {{ deleting ? 'در حال حذف...' : 'حذف تراکنش' }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
