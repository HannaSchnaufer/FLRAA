<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Validasi & Tabel Data dengan Pagination</title>

    <script src="https://cdn.jsdelivr.net/npm/just-validate@3.5.0/dist/just-validate.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 50px; background-color: #f0f0f0; }
        form { margin-bottom: 20px; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        input { width: 100%; padding: 10px; margin-top: 8px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; }
        .is-invalid { border-color: red; }
        .error { color: red; font-size: 12px; margin-top: -10px; margin-bottom: 12px; }

        table { width: 100%; border-collapse: collapse; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #bf00ff; color: white; }

        .loader { width: 48px; height: 48px; border: 5px solid #FFF; border-bottom-color: #FF3D00; border-radius: 50%; display: block; margin: 16px auto; box-sizing: border-box; animation: rotation 1s linear infinite; }
        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        #pagination { margin-top: 20px; text-align: center; }
        .pagination-button { padding: 5px 10px; margin: 0 5px; cursor: pointer; border: 1px solid #ccc; border-radius: 4px; }
        .pagination-button:disabled { background-color: #ddd; cursor: not-allowed; }
    </style>
</head>
<body>

    <form id="dataForm">
        <div>
            <input type="text" name="nik" class="nik" placeholder="Masukkan NIK">
            <p class="error nik--error"></p>
        </div>
        <div>
            <input type="text" name="name" class="name" placeholder="Masukkan Nama">
            <p class="error nama--error"></p>
        </div>
        <button type="submit">Simpan</button>
    </form>

    <label for="perPageSelect">How much u want to see?</label>
    <select id="perPageSelect">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="15">15</option>
        <option value="30">30</option>
    </select>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="pagination"></div>

    <span class="loader" style="display:none;"></span>

    <script>
        let currentPage = 1;

        function loadData(page) {
            const perPageSelect = document.getElementById('perPageSelect');
            const perPage = parseInt(perPageSelect.value);

            const table = document.querySelector('table tbody');
            const loader = document.querySelector('.loader');
            loader.style.display = 'block';

            axios.get(`get-students.php?page=${page}&perPage=${perPage}`).then(response => {
                loader.style.display = 'none';
                const data = response.data;

                if (data.status) {
                    const students = data.students;
                    table.innerHTML = '';

                    students.forEach((student, index) => {
                        const row = `
                            <tr>
                                <td>${(page - 1) * perPage + index + 1}</td>
                                <td>${student.nik}</td>
                                <td>${student.nama}</td>
                            </tr>
                        `;
                        table.innerHTML += row;
                    });

                    displayPagination(data.currentPage, data.totalPages);
                }
            }).catch(error => console.error('Error fetching data:', error));
        }

        //dropdown konten/page
        const perPageSelect = document.getElementById('perPageSelect');
        perPageSelect.addEventListener('change', () => {
            loadData(1);
        });

        function displayPagination(currentPage, totalPages) {
            const paginationContainer = document.getElementById('pagination');
            paginationContainer.innerHTML = '';

            function createButton(page, disabled = false) {
                const button = document.createElement('button');
                button.textContent = page;
                button.classList.add('pagination-button');
                if (page === currentPage) {
                    button.disabled = true;
                }
                button.addEventListener('click', () => {
                    loadData(page);
                });
                paginationContainer.appendChild(button);
            }

            if (currentPage > 1) {
                createButton(1);
            }
            if (currentPage > 3) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                paginationContainer.appendChild(ellipsis);
            }
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                createButton(i);
            }
            if (currentPage < totalPages - 2) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                paginationContainer.appendChild(ellipsis);
            }
            if (currentPage < totalPages) {
                createButton(totalPages);
            }
        }

        // Initial load
        loadData(currentPage);
    </script>

<!-- only God and sepuh programmer know Thell im writing in here -->

</body>
</html>
