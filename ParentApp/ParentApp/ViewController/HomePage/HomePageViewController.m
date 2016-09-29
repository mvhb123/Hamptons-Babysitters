//
//  HomePageViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 07/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//
#import "HomeViewTableViewCell.h"
#import "HomePageViewController.h"
#import "MyProfileViewController.h"
#import "RequestSitterViewController.h"
#import "JobsViewController.h"
#import "ContactUsViewController.h"
#import "HowThisWorkViewController.h"
#import "AboutViewController.h"
#import "JobListViewController.h"
#import "JobDetailViewController.h"
@interface HomePageViewController ()
@end
@implementation HomePageViewController

@synthesize dict_parentRecord;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib
    
   self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:@"US"];
    [[self navigationController] setNavigationBarHidden:NO animated:YES];
    self.navigationItem.title = @"Home";
    self.navigationItem.hidesBackButton = true;
   
    array_parentInfo =  [NSArray arrayWithObjects:@"Request a sitter",@"Jobs",@"How This Works", nil];
    [self setFontTypeAndFontSize];
    
    self.view.backgroundColor=kViewBackGroundColor;
    AppDelegate *appDel = kAppDelegate;
    [appDel reSetNotificationCount];

}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:YES];
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    if ([self.parentInfo.profileStatus isEqualToString:@"1"]) {
        [self setParentInfo];
        if (((self.str_jobId != nil)&&![self.str_jobId isEqualToString:@""])) {
            [self confirmedJobNotfication]; // if notfication message alert is appear.
        }
    }else{
        EditProfileViewController *viewEditProfile=[[EditProfileViewController alloc]initWithNibName:@"EditProfileViewController" bundle:nil];
        [self.navigationController pushViewController:viewEditProfile animated:NO];
    }
    
}
-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
     [self setFontTypeAndFontSize];
    }
#pragma mark -- tableView Deligates.
// tableView DataSource delegate methods.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return array_parentInfo.count;
}
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *CellIdentifier =@"HomeViewTableViewCell";
    HomeViewTableViewCell *cell = (HomeViewTableViewCell *)[self.tbl_parentHome dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
            NSArray *nib = [[NSBundle mainBundle] loadNibNamed:@"HomeViewTableViewCell" owner:self options:nil];
            cell = [nib objectAtIndex:0];
        }
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    cell.backgroundColor = kBackgroundColor
    cell.lbl_ParentButton.text =[array_parentInfo objectAtIndex:indexPath.row];
    return cell;
}
-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    if (indexPath.row == 0) {
        if ([self.parentInfo.profileStatus isEqualToString:@"1"]) {
            RequestSitterViewController *requestSitter = [[RequestSitterViewController alloc]initWithNibName:@"RequestSitterViewController" bundle:nil];
            requestSitter.dict_parentRecord = [dict_parentRecord mutableCopy];
            [self.navigationController pushViewController:requestSitter animated:YES];
        }else{
            [[ApplicationManager getInstance]showAlertForVC:nil withTitle:@"" andMessage:kMsgForIncompleteProfile];
        }
       
    }
    if (indexPath.row == 1) {
        JobsViewController *jobsView = [[JobsViewController alloc]initWithNibName:@"JobsViewController" bundle:nil];
       
        [self.navigationController pushViewController:jobsView animated:YES];

    }
    if (indexPath.row ==2) {
       HowThisWorkViewController *howThisWorkView = [[HowThisWorkViewController alloc]initWithNibName:@"HowThisWorkViewController" bundle:nil];
      
        [self.navigationController pushViewController:howThisWorkView animated:YES];
    }
    if (indexPath.row ==3) {
        AboutViewController *aboutView = [[AboutViewController alloc]initWithNibName:@"AboutViewController" bundle:nil];
        
        [self.navigationController pushViewController:aboutView animated:YES];
    }
    if (indexPath.row ==4) {
        ContactUsViewController *contactUsView = [[ContactUsViewController alloc]initWithNibName:@"ContactUsViewController" bundle:nil];
        
        [self.navigationController pushViewController:contactUsView animated:YES];
    }

}
- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return 0.01;
}

- (CGFloat)tableView:(UITableView *)tableView heightForFooterInSection:(NSInteger)section{
    return 0.05;
}
#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{
    lbl_userName.font=[UIFont fontWithName:RobotoBoldFont size:ButtonFieldFontSize];
    lbl_phone1.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_phone2.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_email.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewEmail.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_viewPhone2.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_viewPhone1.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    [lbl_userName setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_phone1 setTextColor:kLabelColor];
    [lbl_phone2 setTextColor:kLabelColor];
    [lbl_email setTextColor:kLabelColor];
    [lbl_viewPhone2 setTextColor:kLabelColor];
    [lbl_viewPhone1 setTextColor:kLabelColor];
    [lbl_viewEmail setTextColor:kLabelColor];
    [imgDetailBG setBackgroundColor:UIColorFromHexCode(0xe8e8e8)];
    
}
- (IBAction)onClickMyProfile:(id)sender {
    MyProfileViewController *myProfile = [[MyProfileViewController alloc]initWithNibName:@"MyProfileViewController" bundle:nil];
    [self.navigationController pushViewController:myProfile animated:YES];
}
-(void)setParentInfo
{

    lbl_userName.text = self.parentInfo.parentName;
    lbl_phone1.text = [self.numFormatter formatText:self.parentInfo.parentPhone];
    lbl_viewPhone1.text=@"Phone 1:";
    if ([self.parentInfo.parentLocalPhone isEqualToString:@""]) {
       lbl_viewPhone2.text = @"Email:";
       lbl_phone2.text = self.parentInfo.parentUserName;
       lbl_email.hidden = true;
       lbl_viewEmail.hidden = true;
       img_mail.hidden = true;
       lbl_line.hidden = true;
       img_call.image = [UIImage imageNamed:@"Email"];
        
    }else{
        lbl_email.hidden = false;
        lbl_viewEmail.hidden = false;
        img_mail.hidden = false;
        lbl_line.hidden = false;
        img_call.image = [UIImage imageNamed:@"call"];
        lbl_viewPhone2.text = @"Phone 2:";
        lbl_phone2.text = [self.numFormatter formatText:self.parentInfo.parentLocalPhone];
        lbl_email.text = self.parentInfo.parentUserName;
    }
}
// Method is called when notification alert is appear.
-(void)confirmedJobNotfication
{
    NSMutableDictionary *dict_jobDetailData = [[NSMutableDictionary alloc]init];
    [dict_jobDetailData setSafeObject:self.str_jobId forKey:kJobId];
    self.str_jobId = @"";
    JobDetailViewController *jobDetail = [[JobDetailViewController alloc]initWithNibName:@"JobDetailViewController" bundle:nil];
    jobDetail.dict_jobsDetail = [dict_jobDetailData mutableCopy];
    [self.navigationController pushViewController:jobDetail animated:YES];
}
@end
