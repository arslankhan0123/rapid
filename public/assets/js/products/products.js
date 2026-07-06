(() => {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        // Initialize select2 for different elements
        $("#filter_group").select2({ width: "200px" });

        $("#taxSelectTwo, #editTaxSelectTwo").select2({ width: "100%" });
        $("#productGroup, #editProductGroup").select2({
            width: "calc(100% - 44px)",
            placeholder: Lang.get("messages.placeholder.select_product_group"),
        });

        // Initialize Summernote for product descriptions
        $("#productDescription, #editProductDescription").summernote({
            minHeight: 200,
            toolbar: [
                ["style", ["bold", "italic", "underline", "clear"]],
                ["font", ["strikethrough"]],
                ["para", ["paragraph"]],
            ],
        });
    });

    const e = $("#productsTable");

    // Initialize DataTable
    $(e).DataTable({
        oLanguage: {
            sEmptyTable: Lang.get("messages.common.no_data_available_in_table"),
            sInfo: Lang.get("messages.common.data_base_entries"),
            sLengthMenu: Lang.get("messages.common.menu_entry"),
            sInfoEmpty: Lang.get("messages.common.no_entry"),
            sInfoFiltered: Lang.get("messages.common.filter_by"),
            sZeroRecords: Lang.get("messages.common.no_matching"),
        },
        processing: true,
        serverSide: true,
        order: [[2, "desc"]],
        lengthMenu: [
            [-1, 10, 25, 50, 100, 200],
            ["All", 10, 25, 50, 100, 200],
        ],
        pageLength: 10,
        ajax: {
            url: route("products.index"),
            beforeSend: function () {
                startLoader();
            },
            data: function (e) {
                e.group = $("#filter_group").find("option:selected").val();
                e.category = $("#filter_category")
                    .find("option:selected")
                    .val();
            },
            complete: function () {
                stopLoader();
            },
        },
        lengthMenu: [
            [100, 300, 500, 999, -1],
            [100, 300, 500, 999, "All"],
        ],
        pageLength: 100, // Default page length
        columns: [
            {
                data: function (e) {
                    const decodeHtml = (html) => {
                        const txt = document.createElement("textarea");
                        txt.innerHTML = html;
                        return txt.value;
                    };
                    return e.group ? decodeHtml(e.group.name) : "";
                },
                name: "group.name",
                width: "30%",
            },
            {
                data: function (row) {
                    // Decode the main title
                    const t = document.createElement("textarea");
                    t.innerHTML = row.title;
                    let mainTitle = t.value;

                    // Decode Arabic title if exists
                    let arabicTitle = "";
                    if (row.title_arabic) {
                        const a = document.createElement("textarea");
                        a.innerHTML = row.title_arabic;
                        arabicTitle = a.value;
                    }

                    // Return combined string
                    return arabicTitle
                        ? mainTitle + " / " + arabicTitle // Shows like: Title / Arabic Title
                        : mainTitle;
                },
                name: "title",
                width: "40%",
            },

            // {
            //     data: function (e) {
            //         const t = document.createElement("textarea");
            //         t.innerHTML = e.description;
            //         return t.value !== "" ? t.value : "N/A";
            //     },
            //     name: "description",
            //     width: '30%'
            // },
            {
                data: function (e) {
                    const t = [{ id: e.id }];
                    return prepareTemplateRender("#productActionTemplate", t);
                },
                name: "id",
                width: "10%",
            },
        ],
        fnInitComplete: function () {
            $(document).on(
                "change",
                "#filter_group,#filter_category",
                function () {
                    e.DataTable().ajax.reload(null, true);
                }
            );
        },
    });

    // Form submission for adding new products
    $(document).on("submit", "#addNewForm", function (t) {
        t.preventDefault();
        processingBtn("#addNewForm", "#btnSave", "loading");

        const descriptionContent = $("<div />")
            .html($("#productDescription").summernote("code"))
            .text()
            .trim()
            .replace(/ \r\n\t/g, "");
        if ($("#productDescription").summernote("isEmpty")) {
            $("#productDescription").val("");
        } else if (!descriptionContent) {
            displayErrorMessage(
                "Description field cannot contain only white space"
            );
            processingBtn("#addNewForm", "#btnSave", "reset");
            return false;
        }

        $.ajax({
            url: route("products.store"),
            type: "POST",
            data: $(this).serialize(),
            success: function (t) {
                if (t.success) {
                    displaySuccessMessage(t.message);
                    $("#addModal").modal("hide");
                    e.DataTable().ajax.reload(null, false);
                }
            },
            error: function (e) {
                displayErrorMessage(e.responseJSON.message);
            },
            complete: function () {
                processingBtn("#addNewForm", "#btnSave");
            },
        });
    });

    // Edit button click handler
    $(document).on("click", ".edit-btn", function (e) {
        const t = $(e.currentTarget).data("id");
        renderData(t);
    });

    // Render edit data
    window.renderData = function (e) {
        $.ajax({
            url: route("products.edit", e),
            type: "GET",
            success: function (e) {
                if (e.success) {
                    console.log(e.data);
                    $("#itemNumber").val(e.data.item_number);
                    $("#productId").val(e.data.id);
                    const t = document.createElement("textarea");
                    t.innerHTML = e.data.title;
                    $("#editTitle").val(t.value);
                    $("#editTitleArabic").val(e.data.title_arabic);
                    $("#editProductDescription").summernote(
                        "code",
                        e.data.description
                    );
                    $("#editRate").val(e.data.rate);
                    $(".price-input").trigger("input");
                    $("#editTaxSelectOne")
                        .val(e.data.tax_1_id)
                        .trigger("change");
                    $("#editTaxSelectTwo")
                        .val(e.data.tax_2_id)
                        .trigger("change");

                    $("#editserviceGroup")
                        .val(e.data.item_group_id)
                        .trigger("change"); // Set the selected value and trigger change
                    $("#editModal").appendTo("body").modal("show");
                }
            },
            error: function (e) {
                displayErrorMessage(e.responseJSON.message);
            },
        });
    };

    // Form submission for editing products
    $(document).on("submit", "#editForm", function (t) {
        t.preventDefault();
        processingBtn("#editForm", "#btnEditSave", "loading");

        const r = $("#productId").val();
        const a = $("<div />")
            .html($("#editProductDescription").summernote("code"))
            .text()
            .trim()
            .replace(/ \r\n\t/g, "");
        if ($("#editProductDescription").summernote("isEmpty")) {
            $("#editProductDescription").val("");
        } else if (!a) {
            displayErrorMessage(
                "Description field cannot contain only white space"
            );
            processingBtn("#editForm", "#btnEditSave", "reset");
            return false;
        }

        $.ajax({
            url: route("products.update", r),
            type: "PUT",
            data: $(this).serialize(),
            success: function (t) {
                if (t.success) {
                    displaySuccessMessage(t.message);
                    $("#editModal").modal("hide");
                    e.DataTable().ajax.reload(null, false);
                }
            },
            error: function (e) {
                displayErrorMessage(e.responseJSON.message);
            },
            complete: function () {
                processingBtn("#editForm", "#btnEditSave");
            },
        });
    });

    // Delete button click handler
    $(document).on("click", ".delete-btn", function (e) {
        const t = $(e.currentTarget).data("id");
        deleteItem(route("products.destroy", t), "#productsTable", "Service");
    });

    // Modal event handlers for resetting forms
    $("#addModal").on("show.bs.modal", function () {
        $(".note-toolbar-wrapper, .note-toolbar").removeAttr("style");
    });
    $("#editModal").on("show.bs.modal", function () {
        $(".note-toolbar-wrapper, .note-toolbar").removeAttr("style");
    });
    $("#addModal").on("hidden.bs.modal", function () {
        resetModalForm("#addNewForm", "#validationErrorsBox");
        $("#productGroup").val("").trigger("change");
        $("#taxSelectOne").val("").trigger("change");
        $("#taxSelectTwo").val("").trigger("change");
        $("#productDescription").summernote("code", "");
    });
    $("#editModal").on("hidden.bs.modal", function () {
        resetModalForm("#editForm", "#editValidationErrorsBox");
    });

    // Form submission for adding product groups
    $(document).on("submit", "#addProductGroupForm", function (e) {
        e.preventDefault();
        processingBtn("#addProductGroupForm", "#btnSave", "loading");

        $.ajax({
            url: route("product-groups.store"),
            type: "POST",
            data: $(this).serialize(),
            success: function (e) {
                if (e.success) {
                    displaySuccessMessage(e.message);
                    $("#addProductGroupModal").modal("hide");
                    const t = { id: e.data.id, name: e.data.name };
                    const r = new Option(t.name, t.id, false, true);
                    $("#productGroup").append(r).trigger("change");
                }
            },
            error: function (e) {
                displayErrorMessage(e.responseJSON.message);
            },
            complete: function () {
                processingBtn("#addProductGroupForm", "#btnSave");
            },
        });
    });

    $("#addProductGroupModal").on("hidden.bs.modal", function () {
        resetModalForm("#addProductGroupForm", "#validationErrorsBox");
    });

    // Form submission for editing product groups
    $(document).on("submit", "#editProductGroupForm", function (e) {
        e.preventDefault();
        processingBtn("#editProductGroupForm", "#btnSave", "loading");

        $.ajax({
            url: route("product-groups.store"),
            type: "POST",
            data: $(this).serialize(),
            success: function (e) {
                if (e.success) {
                    displaySuccessMessage(e.message);
                    $("#editProductGroupModal").modal("hide");
                    const t = { id: e.data.id, name: e.data.name };
                    const r = new Option(t.name, t.id, false, true);
                    $("#editProductGroup").append(r).trigger("change");
                }
            },
            error: function (e) {
                displayErrorMessage(e.responseJSON.message);
            },
            complete: function () {
                processingBtn("#editProductGroupForm", "#btnSave");
            },
        });
    });

    $("#editProductGroupModal").on("hidden.bs.modal", function () {
        resetModalForm("#editProductGroupForm", "#validationErrorsBox");
    });
})();
