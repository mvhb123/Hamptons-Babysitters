//
//  HomeViewController.h
//  SitterApp
//
//  Created by Vikram gour on 09/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "ProfileViewController.h"
@interface HomeViewController : AppBaseViewController
{

    __weak IBOutlet UIButton *btn_completedJob;
    __weak IBOutlet UIButton *btn_scheduleJob;
    __weak IBOutlet UIButton *btn_openJob;
    __weak IBOutlet UIView *view_userInfo;
    __weak IBOutlet AsyncImageView *img_userProfilePicture;
    __weak IBOutlet AsyncImageView *img_userBackground;
    __weak IBOutlet UIView *viewBlurImg;
    __weak IBOutlet UILabel *lbl_userName;
    __weak IBOutlet UILabel *lbl_userAddress;
    __weak IBOutlet UILabel *lbl_userPhone1;
    __weak IBOutlet UILabel *lbl_userPhone2;
    __weak IBOutlet UILabel *lbl_userEmail;
    __weak IBOutlet UILabel *lbl_line4;
    __weak IBOutlet UITableView *tbl_jobTypeList;
    IBOutlet UITapGestureRecognizer *tapGestureForUserInfo;
    __weak IBOutlet NSLayoutConstraint *con_emailypos;
    __weak IBOutlet NSLayoutConstraint *con_lineypos;
    __weak IBOutlet UIView *viewBottons;
    
}
@property(nonatomic,weak)Sitter *sitterInfo;
- (IBAction)onClick_userInfo:(id)sender;
- (IBAction)onClicked_jobListButtons:(UIButton *)sender;
@end
