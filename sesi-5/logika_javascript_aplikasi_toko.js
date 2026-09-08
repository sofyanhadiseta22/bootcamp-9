// Secret Key default untuk pendaftaran admin
const ADMIN_SECRET_KEY = "ADMIN123";

// Seed data produk awal jika belum ada di LocalStorage
const initialProductsData = [
    { id: 1, name: "Smartphone Galaxy S23 Ultra", price: 9800000, category: "Elektronik", image: "https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=500&q=80", description: "Layar AMOLED 120Hz memukau dengan kamera super presisi 200MP untuk fotografi profesional." },
    { id: 2, name: "Laptop ProBook 14 M2", price: 8500000, category: "Elektronik", image: "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&q=80", description: "Laptop ultra-tipis hemat daya dengan performa kencang untuk komputasi harian dan bisnis." },
    { id: 3, name: "Headphone Noise Cancelling BT", price: 1250000, category: "Elektronik", image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80", description: "Fitur peredam kebisingan aktif dengan bass mendalam dan daya tahan baterai hingga 30 jam." },
    { id: 4, name: "Smartwatch Sport Edition", price: 890000, category: "Elektronik", image: "https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80", description: "Pantau detak jantung, pola tidur, dan langkah kaki Anda secara real-time dengan layar HD." },
    { id: 5, name: "Jaket Denim Vintage Unisex", price: 320000, category: "Pakaian", image: "https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=500&q=80", description: "Jaket bahan jeans tebal berkualitas tinggi dengan potongan kasual kekinian." },
    { id: 6, name: "Kaos Polos Cotton Combed 30s", price: 75000, category: "Pakaian", image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500&q=80", description: "Kaos polos lembut, adem, dan menyerap keringat dengan baik untuk harian." },
    { id: 7, name: "Kemeja Flanel Kotak-Kotak", price: 185000, category: "Pakaian", image: "https://images.unsplash.com/photo-1598032895397-b9472444bf93?w=500&q=80", description: "Kemeja flanel katun halus bermotif klasik yang cocok untuk acara santai maupun formal." },
    { id: 8, name: "Sepatu Sneakers Running Light", price: 420000, category: "Sepatu", image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&q=80", description: "Sneakers lari sangat ringan dengan bantalan empuk meredam benturan." },
    { id: 9, name: "Sepatu Pantofel Kulit Asli", price: 650000, category: "Sepatu", image: "https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?w=500&q=80", description: "Sepatu formal pria berbahan kulit asli dengan jahitan rapi dan elegan." },
    { id: 10, name: "Sepatu High Heels Elegant", price: 380000, category: "Sepatu", image: "https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=500&q=80", description: "Sepatu hak tinggi wanita dengan desain mewah untuk pesta dan acara formal." },
    { id: 11, name: "Kacamata Hitam UV Protection", price: 135000, category: "Aksesoris", image: "https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=500&q=80", description: "Lensa polarized menolak sinar UV400 untuk kenyamanan mata di luar ruangan." },
    { id: 12, name: "Jam Tangan Pria Chronograph", price: 540000, category: "Aksesoris", image: "https://images.unsplash.com/photo-1524805444758-089113d48a6d?w=500&q=80", description: "Tali kulit asli dengan fitur stopwatch digital dan tahan air 30m." },
    { id: 13, name: "Blender Juicer Portable Electric", price: 210000, category: "Rumah Tangga", image: "https://images.unsplash.com/photo-1570222094114-d054a817e56b?w=500&q=80", description: "Membuat jus sehat instan kapan saja dan di mana saja dengan sistem rechargeable USB." },
    { id: 14, name: "Air Fryer Digital 3.5 Liter", price: 720000, category: "Rumah Tangga", image: "https://images.unsplash.com/photo-1585515320310-259814833e62?w=500&q=80", description: "Mengoreng renyah tanpa minyak tambahan untuk pola hidup sehat." }
];

let products = [];
let cart = [];
let selectedProductForModal = null;

function loadDataFromStorage() {
    const storedProds = localStorage.getItem('tokokita_products');
    if (storedProds) {
        products = JSON.parse(storedProds);
    } else {
        products = [...initialProductsData];
        saveProductsToStorage();
    }
}

function saveProductsToStorage() {
    localStorage.setItem('tokokita_products', JSON.stringify(products));
}

function switchView(viewId) {
    document.querySelectorAll('.view-section').forEach(sec => sec.classList.remove('active'));
    const target = document.getElementById(viewId);
    if (target) {
        target.classList.add('active');
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });

    const storeBtn = document.getElementById('navStoreBtn');
    if (viewId === 'storeView') {
        storeBtn.classList.add('active');
    } else {
        storeBtn.classList.remove('active');
    }
}

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(number);
}

function getCategoryBadgeColor(category) {
    switch(category) {
        case 'Elektronik': return 'bg-primary text-white';
        case 'Pakaian': return 'bg-success text-white';
        case 'Sepatu': return 'bg-warning text-dark';
        case 'Aksesoris': return 'bg-info text-dark';
        case 'Rumah Tangga': return 'bg-danger text-white';
        default: return 'bg-secondary text-white';
    }
}

function filterAndRenderProducts() {
    const searchInput = document.getElementById('searchKey');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceRange = document.getElementById('priceRange');
    const sortSelect = document.getElementById('sortSelect');
    const productGrid = document.getElementById('productGrid');
    const emptyState = document.getElementById('emptyState');
    const countNum = document.getElementById('countNum');
    const priceRangeValue = document.getElementById('priceRangeValue');
    const activeFilterBadge = document.getElementById('activeFilterBadge');

    if (!searchInput || !productGrid) return;

    const keyword = searchInput.value.toLowerCase().trim();
    const category = categoryFilter.value;
    const maxPrice = parseInt(priceRange.value, 10);
    const sortMode = sortSelect.value;

    let filtered = products.filter(item => {
        const matchesKeyword = item.name.toLowerCase().includes(keyword) || item.description.toLowerCase().includes(keyword);
        const matchesCategory = (category === 'ALL') || (item.category === category);
        const matchesPrice = item.price <= maxPrice;
        return matchesKeyword && matchesCategory && matchesPrice;
    });

    if (sortMode === 'price-asc') {
        filtered.sort((a, b) => a.price - b.price);
    } else if (sortMode === 'price-desc') {
        filtered.sort((a, b) => b.price - a.price);
    } else if (sortMode === 'name-asc') {
        filtered.sort((a, b) => a.name.localeCompare(b.name));
    } else if (sortMode === 'name-desc') {
        filtered.sort((a, b) => b.name.localeCompare(a.name));
    }

    countNum.innerText = filtered.length;
    priceRangeValue.innerText = formatRupiah(maxPrice);
    activeFilterBadge.innerText = category !== 'ALL' ? `Kategori: ${category}` : 'Semua Kategori';

    productGrid.innerHTML = '';

    if (filtered.length === 0) {
        emptyState.classList.remove('d-none');
    } else {
        emptyState.classList.add('d-none');

        filtered.forEach(product => {
            const badgeClass = getCategoryBadgeColor(product.category);
            const cardCol = document.createElement('div');
            cardCol.className = 'col-6 col-md-4 col-xl-3 d-flex align-items-stretch';
            
            cardCol.innerHTML = `
                <div class="card card-product w-100 shadow-sm d-flex flex-column">
                    <div class="product-img-container">
                        <span class="badge badge-category ${badgeClass}">${product.category}</span>
                        <img src="${product.image}" alt="${product.name}" onerror="this.src='https://placehold.co/400x300/e2e8f0/1e293b?text=Gambar+Produk'">
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <h6 class="card-title fw-bold text-truncate mb-1" title="${product.name}">${product.name}</h6>
                            <p class="card-text text-muted small mb-2 text-truncate" style="font-size: 0.8rem;">${product.description}</p>
                        </div>
                        <div class="mt-2">
                            <div class="price-text mb-2">${formatRupiah(product.price)}</div>
                            <div class="d-grid gap-1">
                                <button class="btn btn-primary btn-sm rounded-pill fw-semibold" onclick="addToCart(${product.id})">
                                    <i class="bi bi-cart-plus me-1"></i> Beli
                                </button>
                                <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="openDetailModal(${product.id})">
                                    Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            productGrid.appendChild(cardCol);
        });
    }
}

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existingIndex = cart.findIndex(item => item.id === productId);
    if (existingIndex > -1) {
        cart[existingIndex].qty += 1;
    } else {
        cart.push({ ...product, qty: 1 });
    }

    updateCartUI();
    showToast(`"${product.name}" ditambahkan ke keranjang!`, "success");
}

function changeCartQty(productId, delta) {
    const index = cart.findIndex(item => item.id === productId);
    if (index > -1) {
        cart[index].qty += delta;
        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }
    }
    updateCartUI();
}

function updateCartUI() {
    const cartCountBadge = document.getElementById('cartCountBadge');
    const cartItemsList = document.getElementById('cartItemsList');
    const emptyCartMessage = document.getElementById('emptyCartMessage');
    const cartTotalPrice = document.getElementById('cartTotalPrice');
    const btnCheckout = document.getElementById('btnCheckout');

    const totalItems = cart.reduce((acc, curr) => acc + curr.qty, 0);
    cartCountBadge.innerText = totalItems;

    cartItemsList.innerHTML = '';

    if (cart.length === 0) {
        emptyCartMessage.classList.remove('d-none');
        btnCheckout.disabled = true;
        cartTotalPrice.innerText = formatRupiah(0);
    } else {
        emptyCartMessage.classList.add('d-none');
        btnCheckout.disabled = false;

        let grandTotal = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.qty;
            grandTotal += subtotal;

            const cartRow = document.createElement('div');
            cartRow.className = 'd-flex align-items-center justify-content-between bg-light p-2 rounded-3';
            cartRow.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <img src="${item.image}" alt="${item.name}" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                    <div>
                        <h6 class="mb-0 fw-bold small text-truncate" style="max-width: 130px;">${item.name}</h6>
                        <span class="text-primary small fw-bold">${formatRupiah(item.price)}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary px-2" onclick="changeCartQty(${item.id}, -1)">-</button>
                        <span class="btn btn-light disabled text-dark px-2 fw-bold" style="min-width: 28px;">${item.qty}</span>
                        <button type="button" class="btn btn-outline-secondary px-2" onclick="changeCartQty(${item.id}, 1)">+</button>
                    </div>
                </div>
            `;
            cartItemsList.appendChild(cartRow);
        });

        cartTotalPrice.innerText = formatRupiah(grandTotal);
    }
}

function openDetailModal(id) {
    const product = products.find(p => p.id === id);
    if (!product) return;

    selectedProductForModal = product;
    document.getElementById('detailModalTitle').innerText = product.name;
    document.getElementById('detailModalName').innerText = product.name;
    document.getElementById('detailModalCategory').innerText = product.category;
    document.getElementById('detailModalCategory').className = `badge mb-2 ${getCategoryBadgeColor(product.category)}`;
    document.getElementById('detailModalPrice').innerText = formatRupiah(product.price);
    document.getElementById('detailModalDescription').innerText = product.description;
    document.getElementById('detailModalImage').src = product.image;

    const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
    modal.show();
}

function showToast(msg, type = "success") {
    document.getElementById('toastMessage').innerText = msg;
    const toastIcon = document.getElementById('toastIcon');

    if (type === "success") {
        toastIcon.className = "bi bi-check-circle-fill text-success fs-5";
    } else if (type === "info") {
        toastIcon.className = "bi bi-info-circle-fill text-info fs-5";
    } else {
        toastIcon.className = "bi bi-exclamation-triangle-fill text-warning fs-5";
    }

    const toastEl = document.getElementById('appToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
}

document.addEventListener('DOMContentLoaded', () => {
    loadDataFromStorage();
    filterAndRenderProducts();

    const searchInput = document.getElementById('searchKey');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceRange = document.getElementById('priceRange');
    const sortSelect = document.getElementById('sortSelect');
    const btnResetFilter = document.getElementById('btnResetFilter');
    const btnAddToCartModal = document.getElementById('btnAddToCartModal');
    const btnCheckout = document.getElementById('btnCheckout');

    if (searchInput) searchInput.addEventListener('input', filterAndRenderProducts);
    if (categoryFilter) categoryFilter.addEventListener('change', filterAndRenderProducts);
    if (priceRange) priceRange.addEventListener('input', filterAndRenderProducts);
    if (sortSelect) sortSelect.addEventListener('change', filterAndRenderProducts);

    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', () => {
            searchInput.value = '';
            categoryFilter.value = 'ALL';
            priceRange.value = 10000000;
            sortSelect.value = 'default';
            filterAndRenderProducts();
        });
    }

    if (btnAddToCartModal) {
        btnAddToCartModal.addEventListener('click', () => {
            if (selectedProductForModal) {
                addToCart(selectedProductForModal.id);
                const modalEl = document.getElementById('productDetailModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        });
    }

    if (btnCheckout) {
        btnCheckout.addEventListener('click', () => {
            showToast("Terima kasih! Pesanan Anda berhasil diproses.", "success");
            cart = [];
            updateCartUI();
            const cartModalEl = document.getElementById('cartModal');
            const modal = bootstrap.Modal.getInstance(cartModalEl);
            if (modal) modal.hide();
        });
    }
});