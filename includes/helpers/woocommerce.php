<?php

namespace Joy_Checkout\Helpers;

function is_thank_you() {
	return \is_checkout() && ! empty( \is_wc_endpoint_url( 'order-received' ) );
}