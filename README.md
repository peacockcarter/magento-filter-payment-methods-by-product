# Filter Payment methods by product

Adds attribute group to all attribute sets. 

Group contains 2 attributes

* Is Enabled
* Multi-select which active payment methods to restrict for that product.

Observer on "payment_method_is_active" event. 

Checks if products in cart are restricted for each payment method.

Removes that payment method if restriction is in place.
