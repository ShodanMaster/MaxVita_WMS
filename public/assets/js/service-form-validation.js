$(function() {
  'use strict';

  $(function() {
    // validate signup form on keyup and submit
    $("#callForm").validate({
      rules: {
        branch: {
          required: true
        },
        customer: {
          required: true
        },
        service_type: {
          required: true
        },
        call_id: {
          required: true
        },
        product: {
          required: true
        },
        category: {
          required: true
        },
        Serialnumber: {
          required: true
        },
        problem: {
          required: true
        },
        actiontaken: {
          required: true,
        },
        callminute: {
          required: true,
		  number: true

        },
        agree: "required"
      },
      messages: {
        branch: {
          required: "Please select a branch"
        },
        customer: {
          required: "Please select a Customer"
        },
        product: {
          required: "Please select a Product"
        },
        category: {
          required: "Please select a Category"
        },
        Serialnumber: {
          required: "Please enter Serial number"
        },
        problem: {
          required: "Please enter Problem"
        },
        actiontaken: {
          required: "Please enter Action taken"
        },
        callminute: {
          required: "Please enter Call minutes",
		  number : "Please enter only numeric value"
        },
        service_type: {
          required: "Please select a Service type"
        },
        call_id: {
          required: "Please select a Call"
        },
      },
      errorPlacement: function(label, element) {
        label.addClass('mt-2 text-danger');
        label.insertAfter(element);
      },
      highlight: function(element, errorClass) {
        $(element).parent().addClass('has-danger')
        $(element).addClass('form-control-danger')
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
          required: true,
          minlength: 5
          
        },
        cntperson: {
          required: true,
          minlength: 5
          
        },
        mobile: {
          required: true,
          minlength: 10,
          maxlength: 10
        },
        pro: {
          required: true,
          
        },
        problem: {
          required: true,
          minlength: 5
        },
        calltype: {
          required: true,
          
        },
        email: {
          required: true,
          email: true
        },
        // topic: {
        //   required: "#newsletter:checked",
        //   minlength: 2
        // },
        agree: "required"
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
          required: "Please enter a address",
          minlength: "serial no must consist of at least 3 characters"
        },
        cntperson: {
          required: "Please enter contact person",
          minlength: "contact person must consist of at least 3 characters"
        },
        mobile: {
          required: "Please enter mobile number",
          minlength: "mobile number consist of at least 10 characters",
          maxlength: "mobile number must consist of only  10 characters"

        },
        calltype: {
          required: "Please select a call type",
        },
        serial_no: {
          required: "Please enter valid serial number",
          minlength: "serial no must consist of at least 10 characters"
          // maxlength: "serial no must consist of at least 10 characters"
        },
        problem: {
          required: "Please enter valid serial number",
          minlength: "serial no must consist of at least 3 characters"
        },
        email: "Please enter a valid email address",
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