document.querySelectorAll(".btn-supprimer").forEach(button => {
    button.addEventListener("click", function() {
        const experienceId = this.getAttribute("data-id");
        const form = document.getElementById(`experience-form-${experienceId}`);

        Swal.fire({
            title: "Êtes-vous sûr ?",
            text: "Cette action est irréversible !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Supprimer définitivement",
            cancelButtonText: "Annuler"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});