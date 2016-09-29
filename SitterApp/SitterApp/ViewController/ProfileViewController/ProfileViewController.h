//
//  ProfileViewController.h
//  SitterApp
//
//  Created by Vikram gour on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "CertificatesAndTrainingCell.h"
#import "EditProfileViewController.h"
@interface ProfileViewController : AppBaseViewController
{

    __weak IBOutlet AsyncImageView *imgUserProfile;
    __weak IBOutlet UIButton *btn_editProfile;
    __weak IBOutlet UILabel *lbl_sitterName;
    __weak IBOutlet UIView *view_mainBG;
    __weak IBOutlet UILabel *lbl_emailValue;
    __weak IBOutlet UILabel *lbl_Phone1Value;
    __weak IBOutlet UILabel *lbl_Phone2Value;
    __weak IBOutlet UILabel *lbl_aboutMe;
    __weak IBOutlet UILabel *lbl_line4;
    __weak IBOutlet UITextView *txtAboutMe;
    __weak IBOutlet UILabel *lblSitterStatus;
    __weak IBOutlet UISwitch *swSitterStatus;
    
    NSInteger yPosition,leftXPosition,RightXPosition,verticalSpacing;
    __weak IBOutlet NSLayoutConstraint *con_emailypos;
  
}
@property(nonatomic,weak)Sitter *sitterInfo;
@property (weak, nonatomic) IBOutlet UITableView *tblAdditionalInfo;
@property(strong,nonatomic)NSMutableArray *array_CertAndTraining,*array_Area,*array_Language,*array_OtherPreferences,*array_ChildPreferences;



- (IBAction)onClicked_editProfile:(id)sender;
- (IBAction)onClickedStatusChange:(id)sender;
-(void)setSitterStatus;
@end
