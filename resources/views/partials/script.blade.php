<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous">
</script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    let table = new DataTable("#myTable", {
        processing: true,
        serverside: true,
        ajax: "{{ url('pegawaiAjax') }}",
        columns: [{
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            {
                data: "nama",
                name: "Nama",
            },
            {
                data: "email",
                name: "Email",
            },
            {
                data: "aksi",
                name: "Aksi",
            },
        ],
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $("body").on("click", ".tombol-tambah", (e) => {
        e.preventDefault();
        $("#exampleModal").modal("show");
        // Proses Create data
        $(".tombol-simpan").click((e) => {
            e.preventDefault();
            simpan();
        })
    });


    // Proses Edit data
    $("body").on("click", ".tombol-edit", (e) => {
        e.preventDefault();
        let id = e.target.attributes["data-id"].value;
        $.ajax({
            url: `pegawaiAjax/${id}/edit`,
            type: "GET",
            success: (response) => {
                $("#exampleModal").modal("show");
                $("#nama").val(response.nama)
                $("#email").val(response.email)
                $(".tombol-simpan").click((e) => {
                    e.preventDefault();
                    simpan(id);
                })
            }
        })
    });

    // Proses Delete data
    $("body").on("click", ".tombol-del", (e) => {
        e.preventDefault()
        if (!confirm("yakin!?")) return

        let id = e.target.attributes["data-id"].value;
        $.ajax({
            url: "pegawaiAjax/" + id,
            type: "DELETE",
            success: () => {
                $("#myTable").DataTable().ajax.reload()
            }
        })
    })

    // fungsi simpan dan update
    const simpan = (id = "") => {
        if (id == "") {
            var url = "pegawaiAjax"
            var type = "POST"
        } else {
            var url = "pegawaiAjax/" + id
            var type = "PUT"
        }
        $.ajax({
            url,
            type,
            data: {
                nama: $("#nama").val(),
                email: $("#email").val(),
            },
            success: (response) => {
                $("#myTable").DataTable().ajax.reload()
                $(".alert-success").removeClass("d-none");
                $(".alert-success").html(response.success);

                // auto close
                setTimeout(() => {
                    $("#exampleModal").modal("hide");
                }, 800);
            },
            error: (response) => {
                let error = response.responseJSON.errors;

                // * Jika menggunakan Jquery bisa seperti ini
                // $.each(error, (key, value) => {
                //     console.log(value)
                // })

                // !ketika diubah menjadi vanila javascript *
                // digunakan untuk mengambil bagian keys dari object, dan keys diubah menjadi array.
                // let keys = Object.keys(error);
                // for (let i = 0; i < keys.length; i++) {
                //     console.log(error[keys[i]][0])
                // }

                // * Versi dari forEach
                $(".alert-danger").html("");
                $(".alert-danger").removeClass("d-none");
                $(".alert-danger").append(`<ul></ul>`)
                Object.keys(error).forEach(e => {
                    $(".alert-danger").find("ul").append(`<li>${error[e][0]}</li>`)
                })
            }
        })
    }

    $("#exampleModal").on("hidden.bs.modal", () => {
        $("#nama").val("");
        $("#email").val("");

        $(".alert-danger").addClass("d-none");
        $(".alert-danger").html("");

        $(".alert-success").addClass("d-none");
        $(".alert-success").html("");
    })
</script>
