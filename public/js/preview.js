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
