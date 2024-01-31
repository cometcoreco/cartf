<script setup>
    import {onMounted, ref} from "vue";

    const emits = defineEmits(['printed']);
    const props = defineProps(['invoice', 'directPrint'])
    let siteInfo = props.invoice.info.site_info;
    let cartInfo = props.invoice.cart;
    let transactionInfo = props.invoice.transaction;
    let pricing = props.invoice.pricing;

    const invoiceContent = ref(null);
    const printInvoice = () => {
        if (invoiceContent.value) {
            const printContent = invoiceContent.value.innerHTML;
            const windowPrint = window.open('', '', 'left=100,top=100,width=300,height=700,toolbar=0,scrollbars=1,status=0');
            windowPrint.document.write(`<link rel="stylesheet" href="${window.appUrl}/assets/tenant/backend/css/pos-invoice.css"/>`);
            windowPrint.document.write(printContent);
            windowPrint.document.close();

            setTimeout(() => {
                windowPrint.focus();

                if (props.directPrint)
                {
                    windowPrint.print();
                }
            }, 250);
            emits('dataPrinted');
        }
    }

    onMounted(() => {
        printInvoice();
    })
</script>

<template>
    <div class="receipt-container invoice" ref="invoiceContent">
        <header class="receipt-header">
            <h1>Money Receipt</h1>
            <p><strong>{{ siteInfo.name }}</strong></p>
            <p><strong>Email:</strong> {{ siteInfo.email }}</p>
            <p><strong>Website:</strong> {{ siteInfo.website }}</p>
            <p><strong>Date:</strong> {{ props.invoice.info.date }}</p>
        </header>
        <div class="receipt-body">
            <table class="receipt-table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in cartInfo" :key="item.name">
                    <td> {{ item.name }}</td>
                    <td>{{ item.qty }}</td>
                    <td>{{ getCurrencySymbolWithAmount(item.price) }}</td>
                    <td>{{ getCurrencySymbolWithAmount(item.subtotal) }}</td>
                </tr>
                <!-- Repeat for each item -->
                </tbody>
            </table>
            <div class="receipt-summary">
                <p><strong>Subtotal:</strong> {{ getCurrencySymbolWithAmount(pricing.sub_total) }}</p>
                <p><strong>Tax ({{ pricing.tax }}%):</strong> {{ getCurrencySymbolWithAmount(pricing.taxed_amount.toFixed(2)) }}</p>
                <p><strong>Total:</strong> {{ getCurrencySymbolWithAmount(pricing.total) }}</p>
                <p><strong>Paid Amount:</strong> {{ getCurrencySymbolWithAmount(transactionInfo.customerPaidAmount) }}</p>
                <p><strong>Change Due:</strong> {{getCurrencySymbolWithAmount(transactionInfo.changeAmount.toFixed(2)) }}</p>
            </div>
        </div>
        <footer class="receipt-footer">
            <p>Thank you for shopping with us!</p>
            <p>Visit our website: {{ siteInfo.website }}</p>
        </footer>
    </div>
</template>
