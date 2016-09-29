//
//  MyProfileViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "NumberFormatter.h"
#import "VerticallyAlignedLabel.h"

@interface MyProfileViewController : AppBaseViewController
{
    __weak IBOutlet UIImageView *img_call;
    __weak IBOutlet UILabel *lbl_line;
    __weak IBOutlet UIImageView *img_mail;
    __weak IBOutlet UILabel *lbl_emergencyContact;
    __weak IBOutlet UILabel *lbl_guardian;
    __weak IBOutlet UIButton *btn_creditsCardDetails;
    __weak IBOutlet UIButton *btn_bookingCredits;
    __weak IBOutlet UIButton *btn_childrenProfile;
    IBOutlet UIView *view_bottom;
    IBOutlet UIView *view_emergencyContact;
    __weak IBOutlet UITableView *tbl_myProfile;
    __weak IBOutlet UIView *view_profile;
    __weak IBOutlet UILabel *lbl_viewBillingAddress;
    __weak IBOutlet UILabel *lbl_viewJobAddress;
    __weak IBOutlet UILabel *lbl_otherContacts;
    __weak IBOutlet UILabel *lbl_viewEmergencyPhone1;
    __weak IBOutlet UILabel *lbl_viewEmergencyContactRelationship;
    __weak IBOutlet UILabel *lbl_viewEmargencyContactName;
    __weak IBOutlet UILabel *lbl_viewGurdianPhone2;
    __weak IBOutlet UILabel *lbl_viewgurdianphone1;
    __weak IBOutlet UILabel *lbl_viewRelationship;
    __weak IBOutlet UILabel *lbl_viewOtherGurdianNAme;
    __weak IBOutlet UILabel *lbl_BillingAddress;
    
    __weak IBOutlet VerticallyAlignedLabel *lbl_JobAddress;
     __weak IBOutlet UILabel *lbl_emergencyContactPhone1;
    __weak IBOutlet UILabel *lbl_emergencyContactRelationship;
    __weak IBOutlet UILabel *lbl_emergencyContactName;
    __weak IBOutlet UILabel *lbl_gurdianPhone2;
    __weak IBOutlet UILabel *lbl_gurdianPhone1;
    __weak IBOutlet UILabel *lbl_relationship;
    __weak IBOutlet UILabel *lbl_otherGurdianName;
    __weak IBOutlet UILabel *lbl_phone1;
    __weak IBOutlet UILabel *lbl_userName;
    __weak IBOutlet UILabel *lbl_phone2;
    __weak IBOutlet UILabel *lbl_viewPhone2;
    __weak IBOutlet UILabel *lbl_viewemail;
    __weak IBOutlet UILabel *lbl_email;
    float contentHight;
    __weak IBOutlet UILabel *lbl_viewPhone1;
    __weak IBOutlet UIImageView *imgUserDetailBG;
    
    int count;
}
@property (nonatomic,strong)NumberFormatter *numFormatter;
@property(nonatomic,strong)NSMutableDictionary *dict_parentRecord;
@property(nonatomic,assign)Parent *parentInfo;
- (IBAction)onClickEditProfile:(id)sender;
- (IBAction)onClickKidsProfile:(id)sender;
- (IBAction)onClickBookingCredits:(id)sender;
- (IBAction)onClickCreditCardDetail:(id)sender;

@end
