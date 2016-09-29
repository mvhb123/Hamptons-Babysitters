//
//  ModifyJobViewController.m
//  SitterApp
//
//  Created by Vikram gour on 22/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "ModifyJobViewController.h"
#import "KidsProfileViewController.h"
@interface ModifyJobViewController ()

@end

@implementation ModifyJobViewController
@synthesize sitterInfo,jobList,indexPath;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.jobList=[ApplicationManager getInstance].jobList;
//    if ([self.isExpired isEqualToString:@"Yes"]) {
//        self.dict_jobData=[self.jobList.array_ScheduledJob safeObjectAtIndex:self.indexPath];
//    }else{
//        self.dict_jobData=[self.jobList.array_ActiveJob safeObjectAtIndex:self.indexPath];
//    }
    
    self.additionalChildId=@"";
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    self.view.backgroundColor=kBackgroundColor;
    self.backgroundScrollView.backgroundColor=kBackgroundColor;
    view_mainBg.backgroundColor=kBackgroundColor;
    [self.backgroundScrollView setHidden:YES];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(addNewChild:) name:kAddChildNotification object:nil];
    [lbl_jobNumberValue setText:[NSString stringWithFormat:@"%@",[self.dict_jobData safeObjectForKey:kJobId]]];
    [txt_startJobValue setText:[NSString stringWithFormat:@"%@",[self.dict_jobData safeObjectForKey:kJobStartDate]]];
    [txt_endJobValue setText:[NSString stringWithFormat:@"%@",[self.dict_jobData safeObjectForKey:kJobEndDate]]];
    [txt_childrenCount setText:[NSString stringWithFormat:@"%@",[self.dict_jobData safeObjectForKey:kActual_child_count]]];
    [txt_aboutJob setText:@""];
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    self.navigationItem.title=@"Modify Job";

}
-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    txt_aboutJob.layer.borderWidth=1.0;
    txt_aboutJob.layer.cornerRadius=3.0;
    txt_aboutJob.layer.borderColor=[UIColor lightGrayColor].CGColor;
    
    
    [self hideDatePicker];
    [self setFontAndColrForView];
    [self.backgroundScrollView setHidden:NO];

}
-(void)viewWillDisappear:(BOOL)animated{
    [super viewWillDisappear:animated];
    [self.view endEditing:YES];
    [txt_aboutJob resignFirstResponder];
}
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    // Adjust frame for iPhone 4s
    if (self.view.bounds.size.height <= 568)
       // view_mainBg.frame = CGRectMake(0, 0, self.view.bounds.size.width,btn_modifyJob.frame.origin.y+btn_modifyJob.frame.size.height);
    [self.backgroundScrollView setContentSize:CGSizeMake(self.view.bounds.size.width,view_mainBg.frame.size.height)];
    DDLogInfo(@"frm main bg %@",NSStringFromCGSize(view_mainBg.frame.size));
}
#pragma mark - UITextViewDelegate

- (void)textViewDidBeginEditing:(UITextView *)textView
{
    [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,textView.frame.origin.y-100) animated:YES];

}

- (BOOL)textView:(UITextView *)textView shouldChangeTextInRange:(NSRange)range replacementText:(NSString *)text {
    
    //    if([text isEqualToString:@"\n"]) {
    //        [textView resignFirstResponder];
    //    }
    return YES;
}
/*
 #pragma mark - Navigation
 
 // In a storyboard-based application, you will often want to do a little preparation before navigation
 - (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
 // Get the new view controller using [segue destinationViewController].
 // Pass the selected object to the new view controller.
 }
 */
#pragma mark - User define methods
-(void)addNewChild:(NSNotification *)notification{
    DDLogInfo(@"child id %@",[notification object]);
    if ([self.additionalChildId isEqualToString:@""]) {
        self.additionalChildId=[NSString stringWithFormat:@"%@",[notification object]];
    }else{
        self.additionalChildId=[self.additionalChildId stringByAppendingString:[NSString stringWithFormat:@",%@",[notification object]]];
    }
    DDLogInfo(@"client id -- %@",self.additionalChildId);
    int childcount=[txt_childrenCount.text intValue];
    childcount++;
    [txt_childrenCount setText:[NSString stringWithFormat:@"%d",childcount]];
     [[NSNotificationCenter defaultCenter] postNotificationName:kUpdateChildCount object:nil];
}
-(void)setFontAndColrForView{
    
    //Heading label
    [lbl_jobNumber setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_startDate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_endDate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_childCount setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_noteAboutJob setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    
    //Value label
    [lbl_jobNumberValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [txt_startJobValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [txt_endJobValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [txt_childrenCount setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [txt_aboutJob setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    
    //Set text color
    [lbl_jobNumber setTextColor:kColorGrayDark];
    [lbl_startDate setTextColor:kColorGrayDark];
    [lbl_endDate setTextColor:kColorGrayDark];
    [lbl_childCount setTextColor:kColorGrayDark];
    [lbl_noteAboutJob setTextColor:kColorGrayDark];
    [lbl_jobNumberValue setTextColor:kColorGrayDark];
    [txt_startJobValue setTextColor:kColorGrayDark];
    [txt_endJobValue setTextColor:kColorGrayDark];
    [txt_childrenCount setTextColor:kColorGrayDark];
    [txt_aboutJob setTextColor:kColorGrayDark];
    
    
}
- (IBAction)onClicked_addChild:(id)sender {
    KidsProfileViewController *viewAddChild=[[KidsProfileViewController alloc]initWithNibName:@"KidsProfileViewController" bundle:nil];
    viewAddChild.checkValue = 1;
    viewAddChild.jobId = [self.dict_jobData safeObjectForKey:kJobId];
    self.navigationItem.title=@"";
    [self.navigationController pushViewController:viewAddChild animated:YES];
   
//    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:kMsgAddChildForModifyJob delegate:self cancelButtonTitle:@"No" otherButtonTitles:@"Yes", nil];
//    [alert setTag:101];
//    [alert show];
}

- (IBAction)onClicked_modifyJob:(id)sender {
    [self.view endEditing:YES];
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:kConfirmationMsgForModifyJob delegate:self cancelButtonTitle:@"No" otherButtonTitles:@"Yes", nil];
        [alert setTag:102];
        [alert show];
    
   
}

- (IBAction)onClick_StartEndDate:(id)sender {
    UIButton *btn=(UIButton*)sender;
    if (btn.tag==0) {
        datePicker.date=[self getDateFromString:txt_startJobValue.text];
        selectedIndex=0;
    }else{
        selectedIndex=1;
        datePicker.date=[self getDateFromString:txt_endJobValue.text];
    }
    [self ShowDatePicker];
    
}
- (IBAction)onClickedDateDonebutton:(id)sender{
    if (selectedIndex==0) {
        [txt_startJobValue setText:[self getDateForAPI:datePicker.date]];
    }else{
        [txt_endJobValue setText:[self getDateForAPI:datePicker.date]];
        
    }
    [self hideDatePicker];
    
}
- (IBAction)onClickedDateCancelbutton:(id)sender{
    [self hideDatePicker];
}
-(void)ShowDatePicker{
    [UIView beginAnimations:@"MoveUp" context:nil];
    [UIView setAnimationDuration:0.3];
    view_datePicker.frame=CGRectMake(0, self.view.frame.size.height-260, view_datePicker.frame.size.width, view_datePicker.frame.size.height);
    [UIView setAnimationDelegate:self];
    [UIView commitAnimations];
    
}
-(void)hideDatePicker{
    [UIView beginAnimations:@"MoveDown" context:nil];
    [UIView setAnimationDuration:0.3];
    view_datePicker.frame=CGRectMake(0, self.view.frame.size.height+20, view_datePicker.frame.size.width, view_datePicker.frame.size.height);
    [UIView setAnimationDelegate:self];
    [UIView commitAnimations];
}
-(NSDate *)getDateFromString:(NSString *)pstrDate
{
    NSDateFormatter* myFormatter = [[NSDateFormatter alloc] init];
    [myFormatter setDateFormat:@"MMM dd, yyyy hh:mm a"];
    NSDate* myDate = [myFormatter dateFromString:pstrDate];
    return myDate;
}
-(NSString*)getDateForAPI:(NSDate*)dt{
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    // Convert date object into desired format
    [dateFormatter setDateFormat:@"MMM dd, yyyy hh:mm a"];
    NSString *newDateString = [dateFormatter stringFromDate:dt];
    return newDateString;
}
- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [txt_aboutJob resignFirstResponder];
}
#pragma mark - AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    
    if (buttonIndex==1 && alertView.tag==102)
    {
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager;
        NSString *string_Url=[NSString stringWithFormat:@"%@",kCompleteJob_API];
        networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
        networkManager.delegate = self;
        NSMutableDictionary *dict_JobRequest=[[NSMutableDictionary alloc] init];
        [dict_JobRequest setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
        [dict_JobRequest setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
        [dict_JobRequest setSafeObject:[self.dict_jobData safeObjectForKey:kJobId] forKey:kJobId];
        [dict_JobRequest setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_JobRequest setSafeObject:txt_startJobValue.text forKey:kActual_start_date];
        [dict_JobRequest setSafeObject:txt_endJobValue.text forKey:kActual_end_date];
        [dict_JobRequest setSafeObject:txt_childrenCount.text forKey:kActual_child_count];
        [dict_JobRequest setSafeObject:txt_aboutJob.text forKey:kNote_aboutJob];
        // [dict_JobRequest setSafeObject:self.additionalChildId forKey:kAddtionalChildId];
        [networkManager completeJob:dict_JobRequest forRequestNumber:1];
    }else if (buttonIndex==1 && alertView.tag==101) {
        int childCount=[[txt_childrenCount text] intValue];
        childCount=childCount+1;
        [txt_childrenCount setText:[NSString stringWithFormat:@"%d",childCount]];
    }else if(alertView.tag==100)
    {
        [self.navigationController popToRootViewControllerAnimated:YES];
    }
    [self appDidLogout:alertView clickedButtonAtIndex:buttonIndex];

}
#pragma mark - SMCoreNetworkManagerDelegate
-(void)requestDidSucceedWithResponseObject:(id)responseObject
                                  withTask:(NSURLSessionDataTask *)task
                             withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
               // [[ApplicationManager getInstance] saveJobList:dict_responseObj andJobType:kOpenJob];//for global use
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_responseObj valueForKey:kMessage] delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
                alert.tag=100;
                [alert show];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
            
            
        default:
            break;
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
}
@end
