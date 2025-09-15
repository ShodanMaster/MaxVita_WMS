$(function() {
  'use strict'

  if ($(".js-example-basic-single").length) {
    $(".js-example-basic-single").select2(
      
    );
  }
  if ($(".js-example-basic-multiple").length) {
    $(".js-example-basic-multiple").select2();
  }

  //$("#customer").select2({
//    ajax: {
//        url:"/searchCustomer",
//        type: "get",
//        dataType: 'json',
//        delay: 250,
//        data: function (params) {
//            return {
//                q: params.term, // search term
//            };
//        },
//        processResults: function (response) {
//          return {
//           results: response
//          };
//          },
//          cache: true
//          
//    }
//});

// $("#customer").select2({
//   ajax: {
//       url:"/searchCustomer",
//       type: "get",
//       dataType: 'json',
//       data: function (params) {
//           return {
//               q: params.term, // search term
//           };
//       },
//       results: function (data) {
//         // alert(data);
//         return {
//           result: data.forEach(data => {
//             return {
//                          text: data.text,
//                          id: data.id
//                      }
//           }),
//         //  results: data.countries.forEach(function (country) {
//         //        return {
//         //            text: country.name,
//         //            id: country.id
//         //        }
//         //    })
//        };
      
//       }
        
//   }
// });

});

