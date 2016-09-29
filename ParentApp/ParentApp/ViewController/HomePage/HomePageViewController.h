//
//  HomePageViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 07/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "NumberFormatter.h"
#import "Parent.h"
#import "EditProfileViewController.h"
@interface HomePageViewController : AppBaseViewController
{
    __weak IBOutlet UIImageView *img_call;
    __weak IBOutlet UILabel *lbl_line;
    __weak IBOutlet UIImageView *img_mail;
    __weak IBOutlet UILabel *lbl_viewPhone1;
    __weak IBOutlet UILabel *lbl_email;
    __weak IBOutlet UILabel *lbl_phone2;
    __weak IBOutlet UILabel *lbl_userName;
    __weak IBOutlet UILabel *lbl_phone1;
    __weak IBOutlet UILabel *lbl_viewPhone2;
    __weak IBOutlet UILabel *lbl_viewEmail;
     NSArray *array_parentInfo;
    __weak IBOutlet UIImageView *imgDetailBG;
    
}

@property (nonatomic,strong)NumberFormatter *numFormatter;
@property (nonatomic ,strong) NSString *str_jobId;
@property (strong, nonatomic) IBOutlet UITableView *tbl_parentHome;
@property (strong, nonatomic) NSMutableDictionary *dict_parentRecord;
@property(nonatomic, assign) Parent *parentInfo;
- (IBAction)onClickMyProfile:(id)sender;
-(void)confirmedJobNotfication;

@end
