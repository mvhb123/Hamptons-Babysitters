//
//  PaymentViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
@interface PaymentViewController : AppBaseViewController
{
    NSString *str_cardInfo;
    NSString *str_nameOnCard; 
    NSString *str_saveCard;
    
 
    __weak IBOutlet UIButton *btn_saveEditCard;
    __weak IBOutlet UILabel *lbl_zip;
    __weak IBOutlet UILabel *lbl_state;
    __weak IBOutlet UILabel *lbl_city;
    __weak IBOutlet UILabel *lbl_streetAddress;
    __weak IBOutlet UILabel *lbl_billingAddress;
    __weak IBOutlet UILabel *lbl_saveCard;
    __weak IBOutlet UILabel *lbl_cvv;
    __weak IBOutlet UILabel *lbl_expiryDate;
    __weak IBOutlet UILabel *lbl_cardNumber;
    __weak IBOutlet UILabel *lbl_nameOnCard;
    __weak IBOutlet UITableView *tbl_stateList;
    __weak IBOutlet UIButton *btn_stateList;
    __weak IBOutlet UIView *view_payment;
    __weak IBOutlet UILabel *lbl_confirmCard;
    __weak IBOutlet UILabel *lbl_EnterBillingAddress;
    
    __weak IBOutlet UIButton *btn_confirmCreditCard;
    __weak IBOutlet UITextField *txt_nameOnCard;
    __weak IBOutlet UIButton *btn_saveAddress;
    __weak IBOutlet UIButton *btn_saveCard;
    __weak IBOutlet UITextField *txt_expiryYear;
    __weak IBOutlet UITextField *txt_cardNumber;
    __weak IBOutlet UITextField *txt_expiryMonth;
    __weak IBOutlet UITextField *txt_cvvNumber;
    __weak IBOutlet UITextField *txt_zip;
    __weak IBOutlet UITextField *txt_state;
    __weak IBOutlet UITextField *txt_city;
    __weak IBOutlet UITextField *txt_streetAddress;
    __weak IBOutlet UITextField *txt_crossStreet;
    
     NSMutableArray *array_statelist;
    NSString *str_stateId;
     float contentHight;
    int checkDropState;
}
@property (nonatomic) int CheckValue;
@property (nonatomic,assign)Parent *parentInfo;
@property(strong,nonatomic)NSMutableDictionary *dict_addJobRequirement;
- (IBAction)onClickCheckSaveCreditCard:(id)sender;
- (IBAction)onClickConfirmCreditCard:(id)sender;
- (IBAction)onClickCheckjobAddressToBillingAddress:(id)sender;
- (IBAction)onClickSaveEditCardDetail:(id)sender;
- (IBAction)onClickStateList:(id)sender;



@end
