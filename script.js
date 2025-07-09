document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    document.getElementById('productForm').addEventListener('submit', saveProduct);
    document.getElementById('cancelEdit').addEventListener('click', resetForm);
});

async function loadProducts() {
    const res = await fetch('api.php?action=list');
    document.getElementById("totalOrders").textContent = "120";
    document.getElementById("totalCustomers").textContent = "75";
    const data = await res.json();
    const tbody = document.querySelector('#productTable tbody');
    tbody.innerHTML = '';
    data.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${p.name}</td>
            <td>${p.price.toFixed(2)}</td>
            <td>${p.quantity}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editProduct(${p.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})">Delete</button>
            </td>`;
        tbody.appendChild(tr);
    });
    updateChart(data);
    document.getElementById('totalProducts').textContent = data.length;
}

function updateChart(products) {
    const ctx = document.getElementById('productChart');
    const labels = products.map(p => p.name);
    const quantities = products.map(p => p.quantity);
    if (window.productChart) {
        window.productChart.destroy();
    }
    window.productChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Quantity', data: quantities, backgroundColor: 'rgba(54, 162, 235, 0.6)' }] },
        options: { responsive: true, animation: { duration: 500 } }
    });
}

async function saveProduct(e) {
    e.preventDefault();
    const id = document.getElementById('productId').value;
    const name = document.getElementById('name').value;
    const price = document.getElementById('price').value;
    const quantity = document.getElementById('quantity').value;
    const action = id ? 'edit' : 'add';
    const formData = new FormData();
    formData.append('name', name);
    formData.append('price', price);
    formData.append('quantity', quantity);
    if (id) formData.append('id', id);
    await fetch(`api.php?action=${action}`, { method: 'POST', body: formData });
    resetForm();
    loadProducts();
}

function editProduct(id) {
    fetch('api.php?action=list').then(r => r.json()).then(data => {
        const prod = data.find(p => p.id == id);
        if (prod) {
            document.getElementById('productId').value = prod.id;
            document.getElementById('name').value = prod.name;
            document.getElementById('price').value = prod.price;
            document.getElementById('quantity').value = prod.quantity;
            document.getElementById('formTitle').textContent = 'Edit Product';
            document.getElementById('cancelEdit').classList.remove('d-none');
        }
    });
}

async function deleteProduct(id) {
    const formData = new FormData();
    formData.append('id', id);
    await fetch('api.php?action=delete', { method: 'POST', body: formData });
    loadProducts();
}

function resetForm() {
    document.getElementById('productId').value = '';
    document.getElementById('productForm').reset();
    document.getElementById('formTitle').textContent = 'Add Product';
    document.getElementById('cancelEdit').classList.add('d-none');
}
