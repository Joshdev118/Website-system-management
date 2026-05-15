// Modal variables
let currentImages = [];
let currentIndex = 0;
let currentItemName = '';

function openModal(itemId, images, itemName, description, price, phone, status) {
    currentImages = images;
    currentIndex = 0;
    currentItemName = itemName;

    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const title = document.getElementById('modalTitle');
    const desc = document.getElementById('modalDescription');
    const priceEl = document.getElementById('modalPrice');
    const contact = document.getElementById('modalContact');
    const statusEl = document.getElementById('modalStatus');
    const thumbnailRow = document.getElementById('thumbnailRow');

    modal.style.display = 'block';
    title.textContent = currentItemName;
    desc.textContent = description;
    priceEl.textContent = '$' + Number(price).toFixed(2);
    contact.textContent = phone;
    contact.href = 'tel:' + phone;
    statusEl.textContent = status;

    updateModalImage();
    renderThumbnails();
    updateNavigationButtons();
}

function updateModalImage() {
    const modalImg = document.getElementById('modalImage');
    modalImg.src = 'uploads/' + currentImages[currentIndex];
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}

function changeImage(direction) {
    currentIndex += direction;
    if (currentIndex >= currentImages.length) {
        currentIndex = 0;
    } else if (currentIndex < 0) {
        currentIndex = currentImages.length - 1;
    }
    updateModalImage();
    highlightThumbnail();
    updateNavigationButtons();
}

function renderThumbnails() {
    const row = document.getElementById('thumbnailRow');
    row.innerHTML = '';
    currentImages.forEach((img, index) => {
        const thumb = document.createElement('img');
        thumb.src = 'uploads/' + img;
        thumb.alt = currentItemName + ' thumbnail ' + (index + 1);
        thumb.className = 'thumbnail-item' + (index === currentIndex ? ' active' : '');
        thumb.addEventListener('click', function() {
            currentIndex = index;
            updateModalImage();
            highlightThumbnail();
            updateNavigationButtons();
        });
        row.appendChild(thumb);
    });
}

function highlightThumbnail() {
    document.querySelectorAll('.thumbnail-item').forEach((thumb, index) => {
        thumb.classList.toggle('active', index === currentIndex);
    });
}

function updateNavigationButtons() {
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    if (currentImages.length <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
    }
}

// Close modal when clicking outside the dialog
document.getElementById('imageModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeModal();
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('imageModal');
    if (modal.style.display === 'block') {
        if (event.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (event.key === 'ArrowRight') {
            changeImage(1);
        } else if (event.key === 'Escape') {
            closeModal();
        }
    }
});