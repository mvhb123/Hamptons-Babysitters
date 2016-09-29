//
//  ProfileViewController.h
//  SitterApp
//
//  Created by Vikram gour on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//
#import "AppBaseViewController.h"
#import "CertificatesAndTrainingCell.h"
#import "NumberFormatter.h"
#import <MessageUI/MessageUI.h>
@interface ProfileViewController :AppBaseViewController<MFMailComposeViewControllerDelegate>
{

    __weak IBOutlet NSLayoutConstraint *constarinUpper;
    __weak IBOutlet UILabel *lbl_line;
    __weak IBOutlet UIImageView *img_email;
    __weak IBOutlet UIImageView *img_call;
    __weak IBOutlet AsyncImageView *imgUserProfile;
    __weak IBOutlet UIButton *btn_editProfile;
    __weak IBOutlet UILabel *lbl_sitterName;
    __weak IBOutlet UIView *view_mainBG;
    __weak IBOutlet UILabel *lbl_emailValue;
    __weak IBOutlet UILabel *lbl_Phone1;
    __weak IBOutlet UILabel *lbl_Phone1Value;
    __weak IBOutlet UILabel *lbl_email;
    __weak IBOutlet UILabel *lbl_Phone2;
    __weak IBOutlet UILabel *lbl_Phone2Value;
    __weak IBOutlet UILabel *lbl_aboutMe;
    __weak IBOutlet UITextView *txtAboutMe;
    NSMutableArray      *arrayForSelectedSection;
    NSInteger yPosition,leftXPosition,RightXPosition,verticalSpacing;
  
}
@property (nonatomic,strong)NumberFormatter *numFormatter;
@property(nonatomic,weak)Sitter *sitterInfo;
@property(nonatomic,strong)NSString *str_sitterId;
@property(nonatomic,assign)Parent *parentInfo;
@property (weak, nonatomic) IBOutlet UITableView *tblAdditionalInfo;
@property(strong,nonatomic)NSMutableArray *array_CertAndTraining,*array_Area,*array_Language,*array_OtherPreferences,*array_ChildPreferences;

@end
