$(function() {
  'use strict';
  $(function() {
    // validate signup form on keyup and submit
    $("#DataForm").validate({
      rules: {
        branch: {
          required: true,

        },
        customer: {
          required: true,
          // minlength: 5
        },
        category: {
          required: true,

        },
        serial_no: {
          required: true,
          minlength: 5
        },
        qc_type: {
          required: true,

        },
        grn_number: {
          required: true,

        },
        contact_no: {
          required: true,

        },
        problem: {
          required: true,
          minlength: 5
        },
        grn_id: {
          required: true,

        },
        agree: "required"
      },
      messages: {
        branch: {
          required: "Please select a branch",

        },
        customer: {
          required: "Please select a Customer",
          // minlength: "Name must consist of at least 3 characters"
        },
        category: {
          required: "Please select a category",

        },
        qc_type: {
          required: "Please select any  Qc type",
        },
        grn_id: {
          required: "Please select a GRN Number",
        },
        serial_no: {
          required: "Please enter valid serial number",
          minlength: "serial no must consist of at least 3 characters"
        },
        grn_number: {
          required: "Please select a GRN Number "
        },
        contact_name: {
          required: "Please enter contact name"
        },
        problem: {
          required: "Please enter valid serial number",
          minlength: "serial no must consist of at least 3 characters"
        },
      },
      errorPlacement: function(label, element) {
        label.addClass('text-danger');
        label.insertAfter(element);
      },
      highlight: function(element, errorClass) {
        $(element).parent().addClass('has-danger')
        $(element).parent().addClass('form-control-danger')
      }
    });
  });
});


$(function() {
  'use strict';


  $(function() {
    // validate signup form on keyup and submit
    $("#addcustomer").validate({
      rules: {
        branch_1: {
          required: true,

        },
        cname: {
          required: true,
          minlength: 5
          // minlength: 5
        },
        address: {
          required: true

        },
        cntperson: {
          required: true,
          minlength: 5

        },
        mobile: {
          required: true,
        }
      },
      messages: {
        branch_1: {
          required: "Please select a branch",

        },
        cname: {
          required: "Please select a  customer name",
          minlength: "Name must consist of at least 5 characters"
        },
        address: {
          required: "Please enter a address"
        },
        cntperson: {
          required: "Please enter contact person",
          minlength: "contact person must consist of at least 3 characters"
        },
        mobile: {
          required: "Please enter mobile number"
        },
      },
      errorPlacement: function(label, element) {
        label.addClass('text-danger');
        label.insertAfter(element);
      },
      highlight: function(element, errorClass) {
        $(element).parent().addClass('has-danger')
        $(element).parent().addClass('form-control-danger')
      }
    });
  });
});
