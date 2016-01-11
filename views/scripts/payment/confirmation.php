<div class="container shop checkout checkout-step-5">
    <?=$this->partial("coreshop/helper/order-steps.php", array("step" => 5));?>

    <p>Ihre Bestellung wurde aufgegeben. Sie haben Cash on Delivery als Zahlungsoption ausgewählt. Wir werden Ihre Bestellung nun verarbeiten.</p>
    <p>Als Zahlungsreferenz geben Sie <?=$this->order->getOrderNumber()?> an</p>

</div>