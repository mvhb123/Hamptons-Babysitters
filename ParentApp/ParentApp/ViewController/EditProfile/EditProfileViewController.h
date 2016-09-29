//
//  EditProfileViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "NumberFormatter.h"

@interface EditProfileViewController : AppBaseViewController
{
    __weak IBOutlet UIButton *btn_childrenProfile;
    __weak IBOutlet UIButton *btn_bookingCredits;
    __weak IBOutlet UIButton *btn_creditCardDetail;
    __weak IBOutlet UIButton *btn_jobAddress;
    __weak IBOutlet UIButton *btn_guardian;
    __weak IBOutlet UILabel *lbl_email;
    __weak IBOutlet UILabel *lbl_phone2;
    __weak IBOutlet UILabel *lbl_phone1;
    __weak IBOutlet UILabel *lbl_relationship;
    __weak IBOutlet UILabel *lbl_lastName;
    __weak IBOutlet UILabel *lbl_firstName;
    __weak IBOutlet UIView *view_editProfile;
    __weak IBOutlet UITextField *txt_relationship;
    __weak IBOutlet UITextField *txt_email;
    __weak IBOutlet UITextField *txt_firstName;
    __weak IBOutlet UITextField *txt_phone2;
    __weak IBOutlet UITextField *txt_phone1;
    __weak IBOutlet UITextField *txt_lastName;
    float contentHight;
}
@property (nonatomic ,strong)NumberFormatter *numFormatter;
@property(nonatomic)int chaeckData;
@property(nonatomic,assign)Parent *parentInfo;
@property(strong,nonatomic)NSMutableDictionary *dict_userProfile;
@property(strong,nonatomic)NSMutableDictionary *dict_updateProfileData;
- (IBAction)onClickEditGurdian:(id)sender;
- (IBAction)onClickJobAddress:(id)sender;
- (IBAction)onClickEditChildrenProfile:(id)sender;
- (IBAction)onClickCreditCardDetail:(id)sender;
- (IBAction)onClickBookingCredits:(id)sender;

@end
