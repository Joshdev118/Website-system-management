// Like functionality
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('like-btn') || e.target.closest('.like-btn')) {
        e.preventDefault();
        const btn = e.target.closest('.like-btn');
        const itemId = btn.dataset.itemId;
        const countSpan = btn.querySelector('.like-count');

        fetch('like_system.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'item_id=' + itemId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                countSpan.textContent = data.count;
                if (data.liked) {
                    btn.classList.add('liked');
                } else {
                    btn.classList.remove('liked');
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
});