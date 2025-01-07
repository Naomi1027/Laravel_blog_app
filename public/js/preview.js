document.addEventListener('DOMContentLoaded', function () {
    const maxTags = 3; // 最大選択可能数
    const checkboxes = document.querySelectorAll('.tag-checkbox');
    const tagError = document.getElementById('tagError');

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const checkedCount = document.querySelectorAll('.tag-checkbox:checked').length;

            if (checkedCount > maxTags) {
                this.checked = false; // チェックを無効化
                tagError.style.display = 'block'; // エラーメッセージを表示
            } else {
                tagError.style.display = 'none'; // エラーメッセージを非表示
            }
        });
    });

    const fileInput = document.getElementById('image');
    const fileError = document.getElementById('fileError');
    const maxSize = 2 * 1024 * 1024; // 2MB in bytes

    document.getElementById('articleForm').addEventListener('submit', function(event) {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            if (file.size > maxSize) {
                event.preventDefault(); // フォーム送信をキャンセル
                fileError.style.display = 'block'; // エラーメッセージを表示
            } else {
                fileError.style.display = 'none'; // エラーメッセージを非表示
            }
        }
    });
});

function setupImagePreview(previewImageId, imageMessageId, fileInputId, fileErrorId, maxSize) {
    const previewImage = document.getElementById(previewImageId);
    const imageMessage = document.getElementById(imageMessageId);
    const fileInput = document.getElementById(fileInputId);
    const fileError = document.getElementById(fileErrorId);

    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (file) {
            if (file.size > maxSize) {
                showError(fileError, previewImage, imageMessage);
            } else {
                hideError(fileError);
                previewFile(file, previewImage, imageMessage);
            }
        } else {
            hidePreview(previewImage, imageMessage);
        }
    });
}

function showError(fileError, previewImage, imageMessage) {
    fileError.style.display = 'block';
    previewImage.style.display = 'none';
    imageMessage.style.display = 'none';
}

function hideError(fileError) {
    fileError.style.display = 'none';
}

function hidePreview(previewImage, imageMessage) {
    previewImage.style.display = 'none';
    imageMessage.style.display = 'none';
}

function previewFile(file, previewImage, imageMessage) {
    const reader = new FileReader();
    reader.onload = function (e) {
        previewImage.src = e.target.result;
        previewImage.style.display = 'block';
        imageMessage.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
