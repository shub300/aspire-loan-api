 
# Mini Aspire Loan API
 
This system is a Mini Aspire Loan app. Which provides a set of APIs to manage loan information. It also gives the ability to make repayments against the loan. Users can simply access these APIs after making authorization.
 
 
This application includes set of API such as:
 
- **Basic:** New User Registration, Login & Logout
- **Loan Module:** It provides facility to Create, Update, Get & Delete loan information.
- **Loan repayment module:** For making payments against the loan.
 
 
## Installation
 
Install laravel package dependencies using the below command
```bash
composer update
```
Setup .env file using .env.example using the below command
```bash
cp .env.example .env
```
 
Migrate database structure using the below command
```bash
php artisan migrate
```
 
For More Information Follow Laravel installation [here](https://laravel.com/docs/8.x/installation).
 
 

## Test Cases
 
Test case can be found inside the tests folder. Written using PHPUnit
 
Run all unit test cases at once 
 
```bash
php artisan test
```
 
Run specific module test cases
 
```bash
php artisan test --filter RegisterTest
php artisan test --filter LoginTest
php artisan test --filter LoanTest
php artisan test --filter RepaymentTest
```
 
## API Collection
 
- [Postman Collection](https://drive.google.com/file/d/10i54ZJhoXXiSh_fDp81TYRFbR45ArI18/view?usp=sharing)
 
## Summary
 
- Users can register and login in the system.
- Authorized users can see their own profile.
- Authorized users can create, get, update, delete loan information. Also will be able to make repayments on Approved loans
- Users are able to make repayments on the Approved loans until its status changes to Paid.
-  Users can only make updates on a loan if it's status is in Pending.