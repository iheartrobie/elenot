document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.menu a').forEach(function(menuItem) {
        menuItem.addEventListener('click', function (event) {
            event.preventDefault();
            var contentName = this.getAttribute('data-content');
            document.querySelectorAll('.content').forEach(function(content) {
                if (content.classList.contains(contentName + '-content')) {
                    content.style.display = 'block';
                } else {
                    content.style.display = 'none';
                }
            });
            document.getElementById('mainContent').style.display = 'none';
            document.getElementById('backButton').style.display = 'block';
        });
    });

    document.getElementById('backButton').addEventListener('click', function () {
        document.querySelectorAll('.content').forEach(function(content) {
            content.style.display = 'none';
        });
        document.getElementById('mainContent').style.display = 'block';
        document.getElementById('backButton').style.display = 'none';
    });

    function toggleAdminSettings() {
        var adminSettings = document.getElementById('adminSettings');
        var mainContent = document.getElementById('mainContent');
        var isSettingsOpen = adminSettings.style.display === 'block';

        if (isSettingsOpen) {
            adminSettings.style.display = 'none';
            mainContent.classList.remove('disabled');
        } else {
            adminSettings.style.display = 'block';
            mainContent.classList.add('disabled');
        }
    }

    function previewProfilePhoto(event) {
        var input = event.target;
        var reader = new FileReader();

        reader.onload = function() {
            var preview = document.getElementById('profilePhotoPreview');
            preview.src = reader.result;
        };

        reader.readAsDataURL(input.files[0]);
    }

    function changeProfilePhoto(event) {
        var input = event.target;
        var profilePic = document.getElementById('profilePic');

        profilePic.src = URL.createObjectURL(input.files[0]);
    }

    document.getElementById('settingsButton').addEventListener('click', toggleAdminSettings);
    document.getElementById('closeSettings').addEventListener('click', toggleAdminSettings);

    document.querySelector('input[name="profile_photo"]').addEventListener('change', previewProfilePhoto);
    document.querySelector('input[name="profile_photo"]').addEventListener('change', changeProfilePhoto);

    // Function to open modals with animation
    function openModalWithAnimation(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = 'block';
        // Adding a class to trigger animation
        modal.classList.add('modal-opened');
    }

    // Edit User Modal
    var editUserModals = document.querySelectorAll('.editUserModal');
    editUserModals.forEach(function(modal) {
        modal.addEventListener('click', function() {
            var userId = modal.getAttribute('data-userid');
            var userEmail = modal.getAttribute('data-useremail');
            var userAccType = modal.getAttribute('data-useracctype');
            var editModal = document.getElementById('editUserModal');

            var editUserIdInput = editModal.querySelector('#editUserId');
            var editUserEmailInput = editModal.querySelector('#editUserEmail');
            var editUserAccTypeInput = editModal.querySelector('#editUserAccType');

            editUserIdInput.value = userId;
            editUserEmailInput.value = userEmail;
            editUserAccTypeInput.value = userAccType;

            // Open modal with animation
            openModalWithAnimation('editUserModal');
        });
    });

    var closeEditModal = document.getElementById('closeEditModal');
    closeEditModal.addEventListener('click', function() {
        var editModal = document.getElementById('editUserModal');
        editModal.style.display = 'none';
    });

    // Delete User Modal
    var deleteUserModals = document.querySelectorAll('.deleteUserModal');
    deleteUserModals.forEach(function(modal) {
        modal.addEventListener('click', function() {
            var userId = modal.getAttribute('data-userid');
            var deleteModal = document.getElementById('deleteUserModal');
            var deleteUserIdInput = deleteModal.querySelector('#deleteUserId');

            deleteUserIdInput.value = userId;

            // Open modal with animation
            openModalWithAnimation('deleteUserModal');
        });
    });

    var closeDeleteModal = document.getElementById('closeDeleteModal');
    closeDeleteModal.addEventListener('click', function() {
        var deleteModal = document.getElementById('deleteUserModal');
        deleteModal.style.display = 'none';
    });

    document.getElementById('cancelDelete').addEventListener('click', function() {
        var deleteModal = document.getElementById('deleteUserModal');
        deleteModal.style.display = 'none';
    });
    

    // Add User Modal
    var addUserModal = document.getElementById('addUserModal');
    var addModalButton = document.getElementById('addUserButton');
    var closeAddModal = document.getElementById('closeAddModal');

    addModalButton.addEventListener('click', function() {
        // Open modal with animation
        openModalWithAnimation('addUserModal');
    });

    closeAddModal.addEventListener('click', function() {
        var addUserModal = document.getElementById('addUserModal');
        addUserModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == addUserModal) {
            addUserModal.style.display = 'none';
        }
    });

});

// Add event listeners for facilities
document.getElementById('addFacilityButton').addEventListener('click', function() {
    document.getElementById('addFacilityModal').style.display = 'block';
});

document.querySelectorAll('.editFacilityModal').forEach(function(button) {
    button.addEventListener('click', function() {
        var facilityId = this.dataset.facility_id;
        var facilityName = this.dataset.facility_name;
        var description = this.dataset.description;
        // populate form fields with existing data
        // show the modal
        document.getElementById('editFacilityModal').style.display = 'block';
    });
});

document.querySelectorAll('.deleteFacilityModal').forEach(function(button) {
    button.addEventListener('click', function() {
        var facilityId = this.dataset.facility_id;
        // populate form fields with existing data
        // show the modal
        document.getElementById('deleteFacilityModal').style.display = 'block';
    });
});

// admindom.js

document.getElementById('closeDeleteFacilityModal').addEventListener('click', function() {
    document.getElementById('deleteFacilityModal').style.display = 'none';
});
