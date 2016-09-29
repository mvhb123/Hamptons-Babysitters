
//
//  HomeViewController.m
//  SitterApp
//
//  Created by Vikram gour on 09/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "HomeViewController.h"
#import "JobViewController.h"
#warning Remove tableview delegate and tableview instance after done this screen.
@interface HomeViewController (){
    NSMutableArray *arrOptionList;
}
@end

@implementation HomeViewController
@synthesize sitterInfo;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    arrOptionList =[[NSMutableArray alloc]initWithObjects:@"Open Jobs Requests",@"Scheduled Jobs",@"Completed Jobs", nil];
    tbl_jobTypeList.separatorStyle=UITableViewCellSeparatorStyleNone;
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    self.view.backgroundColor=kBackgroundColor;
    self.navigationController.navigationBar.titleTextAttributes = @{NSForegroundColorAttributeName:[UIColor whiteColor], NSFontAttributeName:[UIFont fontWithName:Roboto_Regular size:17]};

}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    self.navigationItem.title=@"Home";
}
-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    [btn_openJob.titleLabel setFont:[UIFont fontWithName:Roboto_Light size:FontSize16]];
    [btn_scheduleJob.titleLabel setFont:[UIFont fontWithName:Roboto_Light size:FontSize16]];
    [btn_completedJob.titleLabel setFont:[UIFont fontWithName:Roboto_Light size:FontSize16]];
    
    [img_userProfilePicture loadImageFromURL:self.sitterInfo.sitterProfileImageUrl];
    //[img_userProfilePicture setImageForCurrentView:[UIImage imageNamed:@"noimage.jpg"]];
    [img_userBackground loadImageFromURL:self.sitterInfo.sitterProfileOriginalImageUrl isBlur:NO];

    [viewBlurImg setBackgroundColor:[UIColor colorWithRed:1.0 green:1.0 blue:1.0 alpha:0.8]];
    lbl_userName.text=self.sitterInfo.sitterName;
    
    [lbl_userName setFont:[UIFont fontWithName:Font_Roboto_bold size:FontSize16]];
    NSString *phone1=[NSString stringWithFormat:@" \t\tPhone 1:\t%@",self.sitterInfo.sitterPhone1];
    NSString *phone2=[NSString stringWithFormat:@" \t\tPhone 2:\t%@",self.sitterInfo.sitterPhone2];
    NSString *email= [NSString stringWithFormat:@" \t\tEmail:\t%@",self.sitterInfo.sitterEmail];
    lbl_userEmail.translatesAutoresizingMaskIntoConstraints=YES;

    //Set font color
    lbl_userName.textColor = UIColorFromHexCode(0x04004c);
    [lbl_userPhone1 setTextColor:kColorGrayDark];
    [lbl_userPhone2 setTextColor:kColorGrayDark];
    [lbl_userEmail setTextColor:kColorGrayDark];
    NSTextAttachment *iconPhone = [[NSTextAttachment alloc] init];// Create icon for phone1 lable
    iconPhone.image = [UIImage imageNamed:@"call.png"];
    iconPhone.bounds=CGRectMake(25,-3, 18, 18);
    // For phone 1
    NSMutableAttributedString *strPhone1= [[NSMutableAttributedString alloc]initWithString:phone1];
    NSMutableAttributedString *strPhone=(NSMutableAttributedString*)[NSMutableAttributedString attributedStringWithAttachment:iconPhone];
    NSRange range1 = [phone1 rangeOfString:@" \t\tPhone 1:"];
    [strPhone1 setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Medium size:FontSize12]}
                      range:range1];
    [strPhone1 setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Regular size:FontSize12]}
                     range:NSMakeRange(range1.length,phone1.length-range1.length)];
    
    [strPhone appendAttributedString:strPhone1];

    [lbl_userPhone1 setContentMode:UIViewContentModeTopLeft];
    lbl_userPhone1.attributedText = strPhone;
    //For Phone2
    NSMutableAttributedString *strPhone2=(NSMutableAttributedString*)[NSMutableAttributedString attributedStringWithAttachment:iconPhone];
    NSMutableAttributedString *strUserPhone2= [[NSMutableAttributedString alloc]initWithString:phone2];

    [strUserPhone2 setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Medium size:FontSize12]}
                       range:range1];
    [strUserPhone2 setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Regular size:FontSize12]}
                       range:NSMakeRange(range1.length,phone2.length-range1.length)];
    [strPhone2 appendAttributedString:strUserPhone2];
    [lbl_userPhone2 setContentMode:UIViewContentModeTopLeft];
    lbl_userPhone2.attributedText = strPhone2;
    
    //For email
    NSTextAttachment *iconEmail = [[NSTextAttachment alloc] init];
    iconEmail.image = [UIImage imageNamed:@"mail.png"];
    iconEmail.bounds=CGRectMake(25,-3, 18, 18);
     range1 = [email rangeOfString:@" \t\tEmail:"];
    NSMutableAttributedString *strEmail=(NSMutableAttributedString*)[NSMutableAttributedString attributedStringWithAttachment:iconEmail];
    NSMutableAttributedString *strUserEmail= [[NSMutableAttributedString alloc]initWithString:email];
    [strUserEmail setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Medium size:FontSize12]}
                           range:range1];
    [strUserEmail setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Regular size:FontSize12]}
                           range:NSMakeRange(range1.length,email.length-range1.length)];
    [strEmail appendAttributedString:strUserEmail];
    [lbl_userEmail setContentMode:UIViewContentModeTopLeft];
    lbl_userEmail.attributedText = strEmail;
    
}
-(void)viewDidLayoutSubviews{
    [super viewDidLayoutSubviews];
    //view_userInfo.frame = CGRectMake(0, 0, self.view.bounds.size.width, viewBottons.frame.size.height+viewBottons.frame.origin.y);
    [self.backgroundScrollView setContentSize:CGSizeMake(self.view.bounds.size.width, viewBottons.frame.size.height+viewBottons.frame.origin.y)];
    self.backgroundScrollView.scrollEnabled=YES;
    if ([self.sitterInfo.sitterPhone2 isEqualToString:@""]) {
        [lbl_userPhone2 setAttributedText:lbl_userEmail.attributedText];
      
        [lbl_line4 setHidden:YES];
        [lbl_userEmail setHidden:YES];
      
    }else{
        [lbl_line4 setHidden:NO];
        [lbl_userEmail setHidden:NO];

    }
       DDLogInfo(@"frm %@",NSStringFromCGRect(btn_openJob.frame));
}
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

/*
 #pragma mark - Navigation
 
 // In a storyboard-based application, you will often want to do a little preparation before navigation
 - (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
 // Get the new view controller using [segue destinationViewController].
 // Pass the selected object to the new view controller.
 }
 */
#pragma mark - Userr define method
- (IBAction)onClick_userInfo:(id)sender {
    ProfileViewController *viewProfile=[[ProfileViewController alloc]initWithNibName:@"ProfileViewController" bundle:nil];
    [self.navigationController pushViewController:viewProfile animated:YES];
    
}

- (IBAction)onClicked_jobListButtons:(UIButton *)sender {
    JobViewController *jobViewController=[[JobViewController alloc] initWithNibName:@"JobViewController" bundle:nil];
    switch (sender.tag)
    {
        case 0:
            jobViewController.flag=1;// Open job
            break;
        case 1:
            jobViewController.flag=2;// Scheduled job
            break;
        case 2:
            jobViewController.flag=3; //Active job
            break;
        case 3:
            jobViewController.flag=4;//Complete Job
            break;
        case 4:
            jobViewController.flag=5;//Closed job
            break;
        case 5:
            jobViewController.flag=6;//Cancelled job
            break;
        default:
            break;
    }
    self.navigationItem.title=@"";
    [self.navigationController pushViewController:jobViewController animated:YES];
    
}
/*
 #pragma mark UITableViewDatasource
 
 - (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
 {
 return 50;
 }
 
 - (NSInteger)tableView:(UITableView *)table numberOfRowsInSection:(NSInteger)section
 {
 return [arrOptionList count];
 }
 
 - (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
 {
 static NSString *cellIdentifier = @"OptionCell";
 
 UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier];
 
 if (cell == nil)
 {
 cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:cellIdentifier];
 [cell.textLabel setFont:kOptionListFont];
 [cell.textLabel setTextColor:[UIColor blackColor]];
 [cell setSelectionStyle:UITableViewCellSelectionStyleDefault];
 [cell setBackgroundColor:[UIColor clearColor]];
 cell.accessoryType=UITableViewCellAccessoryDisclosureIndicator;
 cell.textLabel.textAlignment=NSTextAlignmentLeft;
 }
 cell.textLabel.text = [arrOptionList objectAtIndex:indexPath.row];
 
 return cell;
 }
 
 #pragma mark -
 #pragma mark UITableViewDelegate
 
 - (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
 {
 JobViewController *jobViewController=[[JobViewController alloc] initWithNibName:@"JobViewController" bundle:nil];
 switch (indexPath.row)
 {
 case 0:
 jobViewController.flag=1;
 break;
 case 1:
 jobViewController.flag=2;
 break;
 case 2:
 jobViewController.flag=3;
 break;
 default:
 break;
 }
 [self.navigationController pushViewController:jobViewController animated:YES];
 [tableView deselectRowAtIndexPath:indexPath animated:YES];
 }*/
@end
