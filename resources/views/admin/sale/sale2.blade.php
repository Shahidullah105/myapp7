@extends('layouts.master')
 
 @section('content')
     <!-- Start Page Title --> 
     <div class="container">
         <!-- Cart Summary -->
         <div class="cart-summary">
             <h2>Cart</h2>
 
             <!-- Cart Customer Header -->
             <div id="cart-customer">
                 <p>No customer selected for this cart.</p>
             </div>
 
             <!-- Cart Items -->
             <div id="cart-items"></div>
             <div class="summary">
                 <span>Subtotal: $<span id="subtotal">0</span></span><br>
                 <span>Taxes: $<span id="taxes">0</span></span><br>
                 <strong>Total: $<span id="total">0</span></strong><br><br>
                 <button onclick="openPaymentModal()">Proceed to Payment</button>
             </div>
         </div>
 
         <!-- Customer Selection -->
         <div class="customer-section">
             <table class="table table-sm w-100" id="customerTable">
                 <thead class="w-100">
                 <tr class="text-xs text-bold">
                     <td>Customer</td>
                     <td>Pick</td>
                 </tr>
                 </thead>
                 <tbody class="w-100" id="customerList">
                     <!-- Dynamic customer list will be appended here -->
                 </tbody>
             </table>
         </div>
 
         <!-- Product List -->
         <div class="product-list">
             <table class="table w-100" id="productTable">
                 <thead class="w-100">
                 <tr class="text-xs text-bold">
                     <td>Product</td>
                     <td>Pick</td>
                 </tr>
                 </thead>
                 <tbody class="w-100" id="productList">
                     <!-- Dynamic product list will be appended here -->
                 </tbody>
             </table>
         </div>
 
         <!-- Payment Modal -->
         <div id="paymentModal" class="modal">
             <div class="modal-content">
                 <h3>Payment</h3>
                 <label>Total Amount: $<span id="modal-total"></span></label>
                 <input type="number" id="amountReceived" placeholder="Amount Received">
                 <div id="paymentResult"></div>
                 <button onclick="submitCart()">Complete Payment</button>
                 <button class="close" onclick="closePaymentModal()">Close</button>
             </div>
         </div>
     </div>
 
     <script>
         let cart = [];
         let subtotal = 0;
         const taxRate = 0.1;
         let selectedCustomer = null;
 
         // Fetch customer list from server
         customerlist();
         async function customerlist() {
             let response = await fetch('/customerlist');
             let data = await response.json();
             let customerList = document.getElementById('customerList');
             customerList.innerHTML = ''; // Clear existing list
             data.forEach(function(item) {
                 let row = `
                     <tr>
                         <td>${item.name}</td>
                         <td> 
                             <a class="btn btn-dark" onclick="selectCustomer('${item.name}', '${item.address}', '${item.mobile}', '${item.id}')">ADD</a>
                         </td>
                     </tr>
                 `;
                 customerList.innerHTML += row;
             });
         }
 
         // Fetch product list from server
         Productlist();
         async function Productlist() {
             let response = await fetch('/productlist');
             let data = await response.json();
             let productList = document.getElementById('productList');
             productList.innerHTML = ''; // Clear existing list
             data.forEach(function(item) {
                 let row = `
                     <tr>
                         <td>${item.name}</td>
                         <td>
                             <a class="btn btn-dark" onclick="addToCart('${item.name}', ${item.price})">ADD</a>
                         </td>
                     </tr>
                 `;
                 productList.innerHTML += row;
             });
         }
 
         function addToCart(productName, price) {
             const item = cart.find(i => i.name === productName);
             if (item) {
                 item.quantity += 1;
             } else {
                 cart.push({ name: productName, price: price, quantity: 1 });
             }
             updateCart();
         }
 
         function updateCart() {
             const cartItemsDiv = document.getElementById('cart-items');
             cartItemsDiv.innerHTML = '';
             subtotal = 0;
 
             cart.forEach(item => {
                 const itemTotal = item.price * item.quantity;
                 subtotal += itemTotal;
 
                 cartItemsDiv.innerHTML += `
                     <div class="cart-item">
                         <span>${item.name} - $${item.price}</span>
                         <div class="qty-controls">
                             <button onclick="updateQuantity('${item.name}', -1)">-</button>
                             <span>${item.quantity}</span>
                             <button onclick="updateQuantity('${item.name}', 1)">+</button>
                         </div>
                         <button onclick="removeFromCart('${item.name}')">Remove</button>
                     </div>
                 `;
             });
 
             const taxes = subtotal * taxRate;
             const total = subtotal + taxes;
 
             document.getElementById('subtotal').innerText = subtotal.toFixed(3);
             document.getElementById('taxes').innerText = taxes.toFixed(3);
             document.getElementById('total').innerText = total.toFixed(4);
         }
 
         function updateQuantity(productName, change) {
             const item = cart.find(i => i.name === productName);
             if (item) {
                 item.quantity += change;
                 if (item.quantity <= 0) {
                     removeFromCart(productName);
                 } else {
                     updateCart();
                 }
             }
         }
 
         function removeFromCart(productName) {
             cart = cart.filter(i => i.name !== productName);
             updateCart();
         }
 
         function selectCustomer(customerName, address, phone, id) {
             selectedCustomer = { name: customerName, address: address, phone: phone, id: id };
             document.getElementById('cart-customer').innerHTML = `
                 <p>Customer: ${customerName}</p>
                 <p>Address: ${address}</p>
                 <p>Phone: ${phone}</p>
                 <p id="CID">ID: ${id}</p>
             `;
         }
 
         // Payment Modal Functions
         function openPaymentModal() {
             const total = document.getElementById('total').innerText;
             document.getElementById('modal-total').innerText = total;
             document.getElementById('paymentModal').style.display = 'flex';
         }
 
         function closePaymentModal() {
             document.getElementById('paymentModal').style.display = 'none';
         }
 
         // Submit Cart Function (with customer ID, cart items, and payment details)
         async function submitCart() {
             const total = parseFloat(document.getElementById('modal-total').innerText);
             const amountReceived = parseFloat(document.getElementById('amountReceived').value);
             const paymentResultDiv = document.getElementById('paymentResult');
 
             if (!selectedCustomer) {
                 paymentResultDiv.innerText = 'Please select a customer.';
                 return;
             }
 
             if (isNaN(amountReceived)) {
                 paymentResultDiv.innerText = 'Please enter a valid amount.';
                 return;
             }
 
             if (amountReceived < total) {
                 const due = total - amountReceived;
                 paymentResultDiv.innerText = `Payment incomplete! Due Amount: $${due.toFixed(2)}`;
                 return;
             }
 
             // Prepare data to send
             const cartData = {
                 customer_id: selectedCustomer.id,
                 cart: cart,
                 total: total,
                 amount_received: amountReceived,
                 change_due: (amountReceived - total).toFixed(2)
             };
 
             // Send POST request to submit the cart
             try {
                 const response = await fetch('/submitcart', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Laravel CSRF token
                     },
                     body: JSON.stringify(cartData)
                 });
 
                 const result = await response.json();
                 if (response.ok) {
                     paymentResultDiv.innerText = `Payment successful! Change Due: $${cartData.change_due}`;
                     clearCart();
                 } else {
                     paymentResultDiv.innerText = `Error: ${result.message}`;
                 }
             } catch (error) {
                 paymentResultDiv.innerText = `Error submitting cart: ${error.message}`;
             }
         }
 
         function clearCart() {
             cart = [];
             selectedCustomer = null;
             document.getElementById('cart-items').innerHTML = '';
             document.getElementById('cart-customer').innerHTML = '<p>No customer selected for this cart.</p>';
             updateCart();
             closePaymentModal();
         }
     </script>
 @endsection
 