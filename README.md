# Filter Payment methods by product

Adds attribute group to all attribute sets. 

Group contains 4 attributes

* Is Enabled
* Multi-select which active payment methods to restrict for this product.
* list of shipping countries not to apply restriction to.
* list off billing countries not to apply restriction to. 

Observer on "payment_method_is_active" event. 

Checks if products in cart are restricted for each payment method. 

Removes that payment method if restriction is in place.
