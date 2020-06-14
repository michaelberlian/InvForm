THIS IS THE API PROVIDED FOR https://invform.me.
this api domain is https://api.invform.me

this api has 15 routes with 3 major section
these api routes will give a json response. each json response will only by 2 section code and message or data (data/user/message)
the code will be either OK or BAD. OK for successful call and BAD for a BAD call
OK will have data(data/user/message) as the second section of json that will contain the data needed for the front end. 
Code BAD will have message as the second section of json that will contain the error message.

users
this section has 5 routes

register : post method, do not need token, this api need 5 form field( name, email, password, password_confirmation). this route used for register a user. a successful call will give a json fillled with code('OK') and user(containing user data).

login : post method, do not need token, this api need 2 form field ( email, password). this route used for login and get the access_token. a successful call will give unique json from another api. the json will contain code(OK), user(containing user data), and 'access_token'(containing the access_token to be used in future used in the api).

logout : get method, need token on the header. this route used for logout, revoked the given access token. a successful call will give code('OK') and a successful message.

updatepassword : post method, need token on the header, this api need 3 form field (password, new_password, new_password_confirmation). this route is to update the password based on the token owner. a successful call will give code('OK') and a successful message.

userdata : get method, need token on the header. this route is to get access_token owner data. a succesfull call will give code('OK') and a successful message.


inventory stock
this section has 5 routes

create : post method , need access_token on the header, this api need 3 form field (name, quantity, description). this route is used for insert data to the stock table according to the access_token owner. this will insert name, quantity, description based on the form field and created_at and updated_at generated at the current time to the stock table. the id for the data will be generated automatically by the sql. a successful call will give code ('OK') and a successful message.

view : post method , need access_token on the header, this api need 3 form field (name, quantity, description). this route is used for view and search data, this route will search data from the access_token owner's stock table with 3 comparation on Name, Quantity, Description with th provided string on the form field before. this use like sql querry and wildcard at the start and at the end of the string provided at the name and description. while we only use bigger than equal sql querry on the quantity. a successful call will give code ('OK') and data(containing all the filtered or not filtered list from the stock table).

edit/{id} : get method , need access_token on the header. this route used for getting the data from access_token owner stock table based on the id given in the route{id}. a successful call will give code ('OK') and the data(the selected data).

update/{id} : post method , need access_token on the header, this api need 3 form field (name, quantity, description). this route is used for updating data where the id given in the route{id}. the selected data attribute will be updated with the given form field and the updated_at column will be updated with the current time. a successful call will give code('OK') and a successful message. when a data updated on the stock table, all the list from the access_token owner history table itemname that has the same id will also affected if the itemname.

delete{id} : delete method , need access_token on the header. this route used for deleting the data from access_token owner stock table based on the id given in the route{id}. a successful call will give code('OK') and a successful message.


inventory history
this section has 5 routes

history/create : post method , need access_token on the header. this api need 5 form field (itemid, itemname, type, quantity, description). this route is used for insert data to the history table according to the access_token owner. this will insert itemid, itemname, type,  quantity, description based on the form field and created_at and updated_at generated at the current time to the history table. the id for the data will be generated automatically by the sql. a successful call will give code ('OK') and a succesfull message. this history create will affect the quantity of stock table with the accordingly based on itemid, the In/Out type and also the quantity of the history.

history/view : post method , need access_token on the header. this api need 5 form field (itemname, type, description, startdate, enddate). this route is used for searching and viewing the access_token owner history table with 5 comparation on ItemName, Type, Description, updated_at and updated_at with the provided string on the form field before. this use like querry for itemname, type and description , use the bigger than equal on startdata and less than equal on the enddata. a successful call will give code ('OK') and data (containing all the filtered or not filtered list from the history table).

history/edit/{id} : get method , need access_token on the header. this route used for getting the data from access_token owner history table based on the id given in the route{id}. a successful call will give code ('OK') and the data(the selected data). 

history/update/{id} : post method , need access_token on the header, this api need 2 form field (quantity and description). this route is used for updating data where the id given in the route{id}. the selected data attribute will be updated with the given form field and the updated_at column will be updated with the current time. a successful call will give code('OK') and a successful message. this history update will affect the quantity of stock table with the accordingly based on itemid, the In/Out type and also the quantity change on the history update.

history/ing{id} : delete method , need access_token on the header. thsi route used for deleting the data from the access_token owner history table based on the id given in the route{id}. a successful call will give code('OK') and a successful message.
