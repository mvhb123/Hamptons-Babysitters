//
//  RegistrationViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 02/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "NIDropDown.h"
#import "NumberFormatter.h"

@interface RegistrationViewController : AppBaseViewController<NIDropDownDelegate>
{
    __weak IBOutlet UIButton *btn_localAddress;
    __weak IBOutlet UIButton *btn_otherContacts;
    __weak IBOutlet UIView *view_registration;
    __weak IBOutlet UIButton *btn_sex;
    __weak IBOutlet UITableView *tbl_gender;
    __weak IBOutlet UIButton *btn_DOB;
    __weak IBOutlet UIDatePicker *birthdayPicker;
    IBOutlet UIView *View_dob;
    __weak IBOutlet UITextField *txt_email;
    __weak IBOutlet UITextField *txt_phone2;
    __weak IBOutlet UITextField *txt_phone1;
    __weak IBOutlet UITextField *txt_relationship;
    __weak IBOutlet UITextField *txt_firstName;
    __weak IBOutlet UITextField *txt_password;
    __weak IBOutlet UITextField *txt_confirmPassword;
    __weak IBOutlet UITextField *txt_lastName;
    __weak IBOutlet UIButton *btn_ChildrenProfile;
    __weak IBOutlet UILabel *lbl_FirstName;
    __weak IBOutlet UILabel *lbl_LastName;
    __weak IBOutlet UILabel *lbl_Relationship;
    __weak IBOutlet UILabel *lbl_Phone1;
    __weak IBOutlet UILabel *lbl_Phone2;
    __weak IBOutlet UILabel *lbl_Email;
    __weak IBOutlet UILabel *lbl_Password;
    __weak IBOutlet UILabel *lbl_ConfirmPassword;
      NSArray *array_gender;
    float contentHight;
    NSInteger checkDropState;
}
@property (nonatomic ,strong) NumberFormatter *numFormatter;
@property (strong, nonatomic) NIDropDown *dropDown;
@property (strong, nonatomic) NSMutableDictionary *dict_loginData;
@property (strong, nonatomic) NSMutableArray *array_childProfilePic;
- (IBAction)onClickChooseDOB:(id)sender;
- (IBAction)onClickCancleDOB:(id)sender;
- (IBAction)onClickDoneDOB:(id)sender;
- (IBAction)onClickDropDownGender:(id)sender;
- (IBAction)onClickEmergencyContact:(id)sender;
- (IBAction)onClickLocalAddress:(id)sender;
- (IBAction)onClickKidsProfile:(id)sender;
- (IBAction)onClickBillingAddress:(id)sender;
- (IBAction)onClickGuardian:(id)sender;

@end
