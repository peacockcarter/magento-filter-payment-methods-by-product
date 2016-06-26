# Filter Payment methods by product

Adds attribute group to all attribute sets. 

Group contains 3 attributes

* Is Enabled
* Multi-select which active payment methods to restrict for that product.
* list of shipping countries not to apply this rule to

Observer on "payment_method_is_active" event. 

Checks if products in cart are restricted for each payment method. 

Removes that payment method if restriction is in place.
