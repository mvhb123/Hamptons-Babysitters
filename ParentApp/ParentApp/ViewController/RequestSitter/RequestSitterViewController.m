//
//  RequestSitterViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 09/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "RequestSitterViewController.h"
#import "SitterRequirementViewController.h"
#import "PaymentViewController.h"
#import "KidsProfileViewController.h"
#import "ChildProfileImages.h"
#import "CompleteOrderViewController.h"
@interface RequestSitterViewController ()

@end

@implementation RequestSitterViewController

@synthesize dict_parentRecord;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    img_previous.hidden = true;
    tbl_stateList.hidden = true;
    tbl_stateList.layer.borderWidth = 0.5;
    tbl_stateList.layer.borderColor = [UIColor grayColor].CGColor;
    array_childData = [[NSMutableArray alloc]init];
    array_childCount = [[NSMutableArray alloc]init];
    array_childRecord = [[NSMutableArray alloc]init];
    dict_alternateAddress = [[NSMutableDictionary alloc]init];
    [ApplicationManager getInstance].array_selectedChild = [[NSMutableArray alloc]init];
     [ApplicationManager getInstance].array_selectedChildren = [[NSMutableArray alloc]init];
    [txtView_specialInstructions.layer setBorderColor:[[[UIColor grayColor] colorWithAlphaComponent:0.5] CGColor]];
    [txtView_specialInstructions.layer setBorderWidth:1.0];
    txtView_specialInstructions.clipsToBounds = YES;
    [txtView_specialNeed.layer setBorderColor:[[[UIColor grayColor] colorWithAlphaComponent:0.5] CGColor]];
    [txtView_specialNeed.layer setBorderWidth:1.0];
    txtView_specialNeed.clipsToBounds = YES;
    self.navigationItem.title = @"Request a Sitter";
    DDLogInfo(@"dict is %@",dict_parentRecord);
    self.scroll_view.pagingEnabled = YES;
    [btn_alternateAddress setImage:[UIImage imageNamed:@"On"] forState:UIControlStateSelected];
    [btn_alternateAddress setImage:[UIImage imageNamed:@"Off"] forState:UIControlStateNormal];
    [btn_localAddress setSelected:YES];
    [btn_localAddress setImage:[UIImage imageNamed:@"On"] forState:UIControlStateSelected];
    [btn_localAddress setImage:[UIImage imageNamed:@"Off"] forState:UIControlStateNormal];
    NSString *str_localAddress = [NSString stringWithFormat:@""];
    
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",self.parentInfo.StreetAddress]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",self.parentInfo.City]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",self.parentInfo.State]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@ ",self.parentInfo.zipCode]];
    
    if (![self.parentInfo.CrossStreet isEqualToString:@""]) {
        str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",self.parentInfo.CrossStreet]];
    }
    if (![self.parentInfo.HotelName isEqualToString:@""]) {
        str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@",self.parentInfo.HotelName]];
    }
    lbl_localAddress.text = trimedString(str_localAddress);
    array_childRecord = [ApplicationManager getInstance].array_childRecord;
    [self callAPI];
    [view_bottomView setFrame:CGRectMake(0,lbl_alternateAddress.frame.origin.y+30, view_bookChildren.frame.size.width,buttomViewHeight)];
    [view_bookChildren addSubview:view_alternateAddress];
    [view_bookChildren addSubview:view_bottomView];
    view_alternateAddress.hidden = true;
    [self callStateListAPI];
    [self setFontTypeAndFontSize];
    [lbl_specialInstructions setVerticalAlignment:VerticalAlignmentTop];
    checkView = 1;
    self.view.backgroundColor=kViewBackGroundColor;
   view_bottomView.backgroundColor = kViewBackGroundColor;
    view_alternateAddress.backgroundColor = kViewBackGroundColor;

    NSString *strState=[[NSUserDefaults standardUserDefaults] objectForKey:kstateKey];
    [txt_state setText:strState];
    if ([strState isEqualToString:kNewYork]) {
        str_stateId=@"3655";
    }else{
        str_stateId=@"3624";//For California
    }
    buttomViewHeight=220;
}
-(void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    [view_alternateAddress setFrame:CGRectMake(0,lbl_alternateAddress.frame.origin.y+30,view_bookChildren.frame.size.width,261)];
    [view_bookChildren bringSubviewToFront:tbl_stateList];
    [tbl_stateList setFrame:CGRectMake(btn_stateList.frame.origin.x,view_alternateAddress.frame.origin.y+txt_state.frame.origin.y+txt_state.frame.size.height,btn_stateList.frame.size.width,0)];
     [self setFontTypeAndFontSize];
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    self.backgroundScrollView.frame = CGRectMake(0,0,view_bottomView.frame.size.width, self.view.frame.size.height);
    if (btn_specialNeed.selected) {
        [txtView_specialNeed setHidden:NO];
        viewSpecialInsTopConstraint.constant=40;
    }else{
        [txtView_specialNeed setHidden:YES];
        viewSpecialInsTopConstraint.constant=0;
    }
    if (view_alternateAddress.hidden==true){
        [self setLayOutForBottomView:btn_localAddress];
    }
    if (checkView == 1) {
        [self setLayOutForBottomView:btn_localAddress];
    }
    else
    {
        [self setLayOutForBottomView:btn_alternateAddress];
    }
}
-(void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];
    //[self.backgroundScrollView setContentOffset:CGPointMake(0,0) animated:YES];
}
-(void)viewWillAppear:(BOOL)animated
{    [super viewWillAppear:animated];
    [lbl_localAddress setVerticalAlignment:VerticalAlignmentMiddle];
    DDLogInfo(@"preferences is %@",self.array_Preferences);
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(receiveEvent:) name:@"Add/Update Child" object:nil];
    
}
- (void)receiveEvent:(NSNotification *)notification {
    [array_childRecord removeAllObjects];
    [array_childCount removeAllObjects];
    [[ApplicationManager getInstance].array_selectedChild removeAllObjects];
    [self.scroll_view.subviews makeObjectsPerformSelector:@selector(removeFromSuperview)];
    [self callAPI];
}
#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{   // set font type and size
    lbl_selectChildren.font=[UIFont fontWithName:RobotoMediumFont size:ButtonFieldFontSize];
    lbl_childMessage.font=[UIFont fontWithName:RobotoRegularFont size:11];
    lbl_dateTimeHeading.font=[UIFont fontWithName:RobotoMediumFont size:ButtonFieldFontSize];
    lbl_startDateTime.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_endDateTime.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_localAddressHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_localAddress.font = [UIFont fontWithName:RobotoRegularFont size:TextFieldFontSize];
    lbl_alternateAddress.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    txtView_specialInstructions.font = [UIFont fontWithName:RobotoMediumFont size:TextFieldFontSize];
    lbl_hotelName.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_crossStreet.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_streetAddress.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_city.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_state.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_zip.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_specialInstructions.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_jobLocationHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    
    btn_addChild.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    btn_sitterRequirement.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    btn_bookIt.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    btn_specialNeed.titleLabel.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_specialNeed.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    txtView_specialNeed.font = [UIFont fontWithName:RobotoMediumFont size:TextFieldFontSize];
    [lbl_selectChildren setTextColor:kLabelColor];
    [lbl_dateTimeHeading setTextColor:kLabelColor];
    [lbl_startDateTime setTextColor:kLabelColor];
    [lbl_endDateTime setTextColor:kLabelColor];
    [lbl_localAddressHeading setTextColor:kLabelColor];
    [lbl_localAddress setTextColor:kLabelColor];
    [lbl_alternateAddress setTextColor:kLabelColor];
    [lbl_hotelName setTextColor:kLabelColor];
    [lbl_crossStreet setTextColor:kLabelColor];
    [lbl_streetAddress setTextColor:kLabelColor];
    [lbl_city setTextColor:kLabelColor];
    [lbl_state setTextColor:kLabelColor];
    [lbl_zip setTextColor:kLabelColor];
    [lbl_specialInstructions setTextColor:kLabelColor];
    [lbl_specialNeed setTextColor:kLabelColor];
    [lbl_jobLocationHeading setTextColor:kLabelColor];
    [btn_specialNeed setTitleColor:kLabelColor forState:UIControlStateNormal];
     [btn_specialNeed setTitleColor:kLabelColor forState:UIControlStateSelected];
    
}

-(void)callAPI
{
    NSMutableDictionary *dict_kidsProfile=[[NSMutableDictionary alloc] init];
    [dict_kidsProfile setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_kidsProfile setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_kidsProfile setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    // [self startNetworkActivity:YES];
    double delayInSeconds = 0.01;
    dispatch_time_t popTime = dispatch_time(DISPATCH_TIME_NOW, (int64_t)(delayInSeconds * NSEC_PER_SEC));
    dispatch_after(popTime, dispatch_get_main_queue(), ^(void){
        [self startNetworkActivity:YES];
    });
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kChildrenListApi];
    DDLogInfo(@"%@",kChildrenListApi);
    networkManager.delegate = self;
    [networkManager childrenList:dict_kidsProfile forRequestNumber:1];
}
-(void)callStateListAPI
{
    array_statelist = [ApplicationManager getInstance].array_stateList;
    if (array_statelist.count > 0) {
        DDLogInfo(@"State list is %@",array_statelist);
        for (int i=0; i<= array_statelist.count-1 ; i++) {
            [array_states addObject:[[array_statelist objectAtIndex:i]safeObjectForKey:kState]];
        }
        DDLogInfo(@"states is %@",array_states);
        [tbl_stateList reloadData];
    }
    else
    {
        NSMutableDictionary *dict_countryId = [[NSMutableDictionary alloc]init];
        [dict_countryId setSafeObject:@"223" forKey:kCountryId];
        [dict_countryId setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [self startNetworkActivity:YES];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kStateListAPI];
        DDLogInfo(@"%@",kStateListAPI);
        networkManager.delegate = self;
        [networkManager getStateList:dict_countryId forRequestNumber:2];
    }
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
#pragma mark - textField delegates
- (void)textFieldDidBeginEditing:(UITextField *)textField{
    if (textField == txt_state) {
        //[self.view endEditing:YES];
        tbl_stateList.hidden = false;
        [self performSelector:@selector(onClickStateList:) withObject:btn_stateList afterDelay:0.1];
    }
}
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    if (textField!=txt_hotelName){
        return [[view_bookChildren viewWithTag:textField.tag+1] becomeFirstResponder];
    }
    else
    {
        self.backgroundScrollView.contentSize = ScrollContentOffset;
        return [textField resignFirstResponder];
    }
    return true;
}
- (void)textViewDidBeginEditing:(UITextView *)textView {
    DDLogInfo(@"textViewDidBeginEditing:");
    
    if (view_alternateAddress.hidden==true) {
        [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,485) animated:YES];
    }
    else
        [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,735) animated:YES];
    
}
- (void)textViewDidEndEditing:(UITextView *)textView{
    DDLogInfo(@"textViewDidEndEditing:");
    if (view_alternateAddress.hidden==true) {
        [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,200) animated:YES];
    }
    else
    {
        [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,200) animated:YES];
    }
}
- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [self.view endEditing:YES];
    [self.backgroundScrollView setContentOffset:CGPointMake(0, 0) animated:YES];
    self.backgroundScrollView.contentSize = ScrollContentOffset;
}
- (IBAction)onClickBookSitterrequirement:(id)sender {
    [self.view endEditing:YES];
    SitterRequirementViewController *sitterRequirement = [[SitterRequirementViewController alloc]initWithNibName:@"SitterRequirementViewController" bundle:nil];
    sitterRequirement.checkValue = 1;
    sitterRequirement.array_childPreference = [self.array_Preferences mutableCopy];
    [self.navigationController pushViewController:sitterRequirement animated:YES];
    
}
- (void)scrollViewDidScroll:(UIScrollView *)scrolView {
    //img_previous.hidden = true;
    if (array_childRecord.count<=3) {
        img_next.hidden = true;
        img_previous.hidden =true;
    }
    else
    {
    if (!((int)scrolView.contentOffset.x % (int)scrolView.frame.size.width) == 0) {
        img_next.hidden = true;
        img_previous.hidden =false;
    }else if ((int)scrolView.contentOffset.x==0){
        img_previous.hidden =true;
        img_next.hidden = false;

    }
    else{
        img_next.hidden = false;
    }
    }
    
}
- (IBAction)onClickBookforPayment:(id)sender {
    [self.view endEditing:YES];
    
    NSTimeInterval interval = [endTime timeIntervalSinceDate:startTime];
    NSLog(@"time inteval is %f",interval);
    int hour = (interval/3600);
    txtView_specialInstructions.text = trimedString(txtView_specialInstructions.text);
    txtView_specialNeed.text = trimedString(txtView_specialNeed.text);
    NSMutableDictionary *dict_addJobRequirement = [[NSMutableDictionary alloc]init];
    if ([ApplicationManager getInstance].array_selectedChild.count == 0) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kSelectChild];
    }
    else if ([txt_startDate.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterStartDate];
    }
    else if ([txt_endDate.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterEndDate];
    }else if (btn_specialNeed.selected && [txtView_specialNeed.text isEqualToString:@""])
    {
            [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterSpecialNeed];
    }
    else
    {
        NSDateFormatter * dateFormatter = [[NSDateFormatter alloc]init];
        [dateFormatter setDateFormat:@"MMM dd, yyyy hh:mm a"];
        NSDate * startDate = [dateFormatter dateFromString:txt_startDate.text];
        NSDate * endDate  =  [dateFormatter dateFromString:txt_endDate.text];
        
        if([startDate timeIntervalSinceDate:endDate] >= 0 ) {
            [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kCheckStartEndDate];
            
        }
        else if(hour<3 && hour>-3)
        {
             [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kStartEndTime];
        }
        else
        {   [ApplicationManager getInstance].str_startDate = txt_startDate.text;
            [dict_addJobRequirement setSafeObject:txt_startDate.text forKey:kJobStartDate];
            [dict_addJobRequirement setSafeObject:txt_endDate.text forKey:kJobEndDate];
            [dict_addJobRequirement setSafeObject:self.array_Preferences forKey:kPreferences];
            
            [dict_addJobRequirement setSafeObject:txtView_specialInstructions.text forKey:kChildNotes];
             if (btn_specialNeed.selected)
            {
                [dict_addJobRequirement setSafeObject:txtView_specialNeed.text forKey:@"special_need"];
                [dict_addJobRequirement setSafeObject:@"1" forKey:kisSpecialNeed];
            }else{
                [dict_addJobRequirement setSafeObject:@"0" forKey:kisSpecialNeed];
            }
            
            if (view_alternateAddress.hidden==true) {
                [dict_addJobRequirement setSafeObject:self.parentInfo.dict_parentLocalAddress forKey:kLocalAddress];
                if ([self.parentInfo.authrizedPaymentProfileId isEqualToString:@""]) {
                    PaymentViewController *paymentView = [[PaymentViewController alloc]initWithNibName:@"PaymentViewController" bundle:nil];
                    paymentView.dict_addJobRequirement = [dict_addJobRequirement mutableCopy];
                    [self.navigationController pushViewController:paymentView animated:YES];
                }
                else
                {
                    [dict_addJobRequirement setSafeObject:self.parentInfo.authrizedPaymentProfileId forKey:kAutherizePaymentId];
                    CompleteOrderViewController *completeOrder = [[CompleteOrderViewController alloc]initWithNibName:@"CompleteOrderViewController" bundle:nil];
                    completeOrder.dict_addJobRequirement = [dict_addJobRequirement mutableCopy];
                    [self.navigationController pushViewController:completeOrder animated:YES];
                    
                }
                
            }
            else
            {
                if ([self checkValue]) {
                    [dict_alternateAddress setSafeObject:txt_hotelName.text forKey:kHotelName];
                    [dict_alternateAddress setSafeObject:txt_crossStreet.text forKey:kCrossStreet];
                    [dict_alternateAddress setSafeObject:txt_streetAddress.text forKey:kStreetAddress];
                    [dict_alternateAddress setSafeObject:txt_city.text forKey:kCity];
                    [dict_alternateAddress setSafeObject:txt_state.text forKey:kState];
                    [dict_alternateAddress setSafeObject:txt_zip.text forKey:kZip];
                    [dict_alternateAddress setSafeObject:str_stateId forKey:kStateId];
                    [dict_alternateAddress setSafeObject:kOther forKey:kAddressType];
                    [dict_addJobRequirement setSafeObject:dict_alternateAddress forKey:kLocalAddress];
                    if ([self.parentInfo.authrizedPaymentProfileId isEqualToString:@""]) {
                        PaymentViewController *paymentView = [[PaymentViewController alloc]initWithNibName:@"PaymentViewController" bundle:nil];
                        paymentView.dict_addJobRequirement = [dict_addJobRequirement mutableCopy];
                        [self.navigationController pushViewController:paymentView animated:YES];
                    }
                    else
                    {
                        [dict_addJobRequirement setSafeObject:self.parentInfo.authrizedPaymentProfileId forKey:kAutherizePaymentId];
                        CompleteOrderViewController *completeOrder = [[CompleteOrderViewController alloc]initWithNibName:@"CompleteOrderViewController" bundle:nil];
                        completeOrder.dict_addJobRequirement = [dict_addJobRequirement mutableCopy];
                        [self.navigationController pushViewController:completeOrder animated:YES];
                        
                    }
                }
            }
        }
    }
}
-(BOOL)checkValue
{
    BOOL isvalid = NO;
    txt_state.text = trimedString(txt_state.text);
    txt_zip.text = trimedString(txt_zip.text);
    txt_streetAddress.text = trimedString(txt_streetAddress.text);
    txt_city.text = trimedString(txt_city.text);
    if (txt_streetAddress==nil|| [txt_streetAddress.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterStreetAddress];
        isvalid=NO;
    }
    else if (txt_city.text==nil|| [txt_city.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterCity];
        isvalid = NO;
    }
    else if (txt_state.text==nil|| [txt_state.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterState];
        isvalid = NO;
    }
    else if (![txt_zip.text isEqualToString:@""])
    {
        if (![[ValidationManager getInstance]validateZip:txt_zip.text])
        {
            [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidzip];
            isvalid = NO;
        }else{
            isvalid=YES;
        }
    }
    else
    {
        isvalid = YES;
    }
    return isvalid;
}
- (IBAction)onClickAddChild:(id)sender {
    KidsProfileViewController *kidsProfile = [[KidsProfileViewController alloc]initWithNibName:@"KidsProfileViewController" bundle:nil];
    kidsProfile.checkValue = 1;
    [ApplicationManager getInstance].array_selectedChildren = [[[ApplicationManager getInstance]array_selectedChild]mutableCopy];
    [self.navigationController pushViewController:kidsProfile animated:YES];
    
}
- (IBAction)onClickDoneDate:(id)sender {
    NSDateFormatter *format = [[NSDateFormatter alloc] init];
    NSDate *date = datePicker.date;
    
    //[format setDateFormat:@"dd MMM yyyy hh:mm a"];
    [format setDateFormat:@"MMM dd, yyyy hh:mm a"];
    NSString *dateString = [format stringFromDate:date];
    datePicker.maximumDate=[NSDate date];
    [view_datePicker setFrame:CGRectMake(0,kScreenHeight+10, view_datePicker.frame.size.width, view_datePicker.frame.size.height)];
    if (CheckTagValue == 1)
    {
    txt_startDate.text=[NSString stringWithFormat:@" %@",dateString];

        NSDateFormatter* df1 = [[NSDateFormatter alloc]init];
        [df1 setDateFormat:@"HH:mm"];
        [df1 setDefaultDate:[format dateFromString:[format stringFromDate:[NSDate date]]]];
        NSString *time = [df1 stringFromDate:date];
        startTime = [df1 dateFromString:time];
        
        DDLogInfo(@" start time is %@",startTime);
    }
    else
    {
        txt_endDate.text = [NSString stringWithFormat:@" %@",dateString];
        NSDateFormatter* df1 = [[NSDateFormatter alloc]init];
        [df1 setDefaultDate:[format dateFromString:[format stringFromDate:[NSDate date]]]];
        [df1 setDateFormat:@"HH:mm"];
        NSString *time = [df1 stringFromDate:date];
        endTime = [df1 dateFromString:time];
        DDLogInfo(@" EndTime time is %@",endTime);
    }

    datePicker.date=date;
    [view_datePicker removeFromSuperview];
    
}
- (IBAction)onClickCancleDate:(id)sender {
    [view_datePicker setFrame:CGRectMake(0,kScreenHeight+10, view_datePicker.frame.size.width, view_datePicker.frame.size.height)];
    [view_datePicker removeFromSuperview];
    
}
- (IBAction)onClickChooseDate:(id)sender {
    UIButton *btn = sender;
    CheckTagValue = btn.tag;
    
    NSDateFormatter * dateFormatter = [[NSDateFormatter alloc]init];
    [dateFormatter setDateFormat:@"MMM dd, yyyy hh:mm a"];
    NSDate *requiredStartdDate;
    NSDate * startDate;
    if ([txt_startDate.text isEqualToString:@""]){
        NSDate *now = [NSDate date];
        requiredStartdDate = [self getRequiredDateFromCurrentTime:now];//[now dateByAddingTimeInterval:300];
    }else{
        startDate = [dateFormatter dateFromString:txt_startDate.text];
        requiredStartdDate = [self getRequiredDateFromCurrentTime:startDate];
    }
    NSCalendar* calendar = [NSCalendar currentCalendar] ;
    NSDateComponents* twoYear = [NSDateComponents new] ;
    twoYear.month = 6 ;// For max date
    NSDate* addTwoYear = [calendar dateByAddingComponents:twoYear toDate:[NSDate date] options:0];
    if ([txt_startDate.text isEqualToString:@""]) {
        datePicker.date = requiredStartdDate ;
        datePicker.minimumDate = requiredStartdDate;
   
    }
    else
    {
        if (CheckTagValue == 2) {
            NSDateComponents* comp = [NSDateComponents new] ;
            comp.minute = 60*3;//For add three hrs
            NSDate* addMinutes = [calendar dateByAddingComponents:comp toDate:startDate options:0] ;
            datePicker.minimumDate = addMinutes;
         
        }else
        {   NSDate *now = [NSDate date];
            datePicker.date = [self getRequiredDateFromCurrentTime:now];
            datePicker.minimumDate = [self getRequiredDateFromCurrentTime:now];
            txt_endDate.text = @"";
        }
        
    }
    datePicker.maximumDate = addTwoYear;
    [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,135) animated:YES];
    [self.view endEditing:YES];
    [view_datePicker setFrame:CGRectMake(0,(kScreenHeight-310), self.view.frame.size.width, view_datePicker.frame.size.height)];
    [self.view addSubview:view_datePicker];
}
-(NSDate*)getRequiredDateFromCurrentTime:(NSDate*)now
{
   
    NSCalendar *calendar = [NSCalendar currentCalendar];
    NSDateComponents *components = [calendar components:(NSHourCalendarUnit | NSMinuteCalendarUnit) fromDate:now];
   // NSInteger hour = [components hour];
    NSInteger minute = [components minute];
    NSInteger minuteWith15Inter=(15-(minute%15));
    NSDate *dt=[now dateByAddingTimeInterval:minuteWith15Inter*60];
    DDLogInfo(@"dt %@",dt);
    return  dt ;

}
- (IBAction)onClickAlternateAddress:(id)sender {
    [self.view endEditing:YES];
    UIButton *btn=(UIButton*)sender;
    DDLogInfo(@"tag is %ld",(long)btn.tag);
    checkView = (int)btn.tag;
    [self viewDidLayoutSubviews];
    // [self setLayOutForBottomView:btn];
}

- (IBAction)onClickStateList:(id)sender {
    [self.backgroundScrollView removeGestureRecognizer:self.tapRecognizer];
    [self.view endEditing:YES];
    tbl_stateList.hidden = false;
    [self.backgroundScrollView bringSubviewToFront:view_alternateAddress];
    if (checkDropState == 0) {
        [view_bookChildren bringSubviewToFront:tbl_stateList];
        
        [tbl_stateList setFrame:CGRectMake(btn_stateList.frame.origin.x,tbl_stateList.frame.origin.y,btn_stateList.frame.size.width,150)];
        checkDropState = 1;
    }else{
        [UIView beginAnimations:@"view_state" context: nil];
        [UIView setAnimationBeginsFromCurrentState:YES];
        [UIView setAnimationDuration:0.25];
        [tbl_stateList setFrame:CGRectMake(btn_stateList.frame.origin.x, tbl_stateList.frame.origin.y,btn_stateList.frame.size.width,0)];
        [UIView commitAnimations ];
        checkDropState = 0;
        
    }
    
}

- (IBAction)onClickSpecialNeed:(id)sender {
    btn_specialNeed.selected=!btn_specialNeed.selected;
    if (btn_specialNeed.selected) {
        [txtView_specialNeed setHidden:NO];
        viewSpecialInsTopConstraint.constant=40;
        buttomViewHeight=buttomViewHeight+40;
//        CGRect frm=view_bottomView.frame;
//        frm.size.height=frm.size.height+45;
//        [view_bottomView setFrame:frm];
    }else{
        [txtView_specialNeed setText:@""];
        [txtView_specialNeed setHidden:YES];
        viewSpecialInsTopConstraint.constant=0;
         buttomViewHeight=buttomViewHeight-40;
//        CGRect frm=view_bottomView.frame;
//        frm.size.height=frm.size.height-45;
//        [view_bottomView setFrame:frm];
    }
    if (view_alternateAddress.hidden==true){
        [self setLayOutForBottomView:btn_localAddress];
    }
    if (checkView == 1) {
        [self setLayOutForBottomView:btn_localAddress];
    }
    else
    {
        [self setLayOutForBottomView:btn_alternateAddress];
    }
}
-(void)setLayOutForBottomView:(UIButton *)btn
{
    if (btn.tag == 2) {
        [btn_alternateAddress setSelected:YES];
        [btn_localAddress setSelected:NO];
        view_alternateAddress.hidden = false;
        [view_bottomView setFrame:CGRectMake(0,view_alternateAddress.frame.origin.y+view_alternateAddress.frame.size.height+10, view_bookChildren.frame.size.width,buttomViewHeight)];
        [view_bookChildren setFrame:CGRectMake(0,view_bookChildren.frame.origin.y, view_bookChildren.frame.size.width,view_bottomView.frame.origin.y+view_bottomView.frame.size.height)];
        contentHight = 0;
        UIView *lLast =view_bottomView; //[view_bookChildren.subviews lastObject];
        NSInteger wd = lLast.frame.origin.y;
        NSInteger ht = lLast.frame.size.height;
        contentHight = wd+ht;
        DDLogInfo(@"bottom  hight %f",view_bookChildren.frame.size.height);
        DDLogInfo(@"content hight %f",contentHight);
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,MAX(contentHight, (view_bookChildren.frame.size.height+view_alternateAddress.frame.size.height-50)));
         //self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight);
        self.backgroundScrollView.scrollEnabled = YES;
    }
    else
    {
        [btn_alternateAddress setSelected:NO];
        [btn_localAddress setSelected:YES];
        view_alternateAddress.hidden = true;
        [view_bottomView setFrame:CGRectMake(0,lbl_alternateAddress.frame.origin.y+30, view_bookChildren.frame.size.width, buttomViewHeight)];
        contentHight = 0;
        UIView *lLast = view_bottomView;//[view_bookChildren.subviews lastObject];
        NSInteger wd = lLast.frame.origin.y;
        NSInteger ht = lLast.frame.size.height;
        contentHight = wd+ht;
        if (btn_specialNeed.selected) {
            self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,MAX(contentHight, (view_bookChildren.frame.size.height-90)));
        }else{
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,MAX(contentHight, (view_bookChildren.frame.size.height-130)));
        }
    }
    ScrollContentOffset = self.backgroundScrollView.contentSize;
}
-(void)setDataInScrollView
{    int j=0;
    
    //DDLogInfo(@"array children detail %@",array_childRecord);
    //array_childRecord = [[NSMutableArray alloc]init];
   // DDLogInfo(@"array is %@",[ApplicationManager getInstance].array_childRecord);
    array_childRecord = [ApplicationManager getInstance].array_childRecord;
    NSArray *viewsToRemove = [self.scroll_view subviews];
    for (ChildProfileImages *v in viewsToRemove)
        [v removeFromSuperview];
    for (int i=0; i<array_childRecord.count; i++)
    {
        ChildProfileImages *childProfile;
        childrenInfo = [array_childRecord safeObjectAtIndex:i];
        NSArray *nibArray = [[NSBundle mainBundle] loadNibNamed:@"ChildProfileImages" owner:self options:nil];
        childProfile = [nibArray safeObjectAtIndex:0];
        [childProfile setFrame:CGRectMake(j,4, childProfile.frame.size.width, childProfile.frame.size.height)];
        j=j+childProfile.frame.size.width;
        childProfile.lbl_childName.text = childrenInfo.childName;
        childProfile.btn_selectImage.tag = i;
        childProfile.img_checkedChild.tag = i;
        childProfile.img_checkedChild.image = nil;
        [childProfile.btn_selectImage setBackgroundImage:[UIImage imageNamed:@"right"] forState:UIControlStateSelected];
        [childProfile.btn_selectImage setBackgroundImage:[UIImage imageNamed:@""] forState:UIControlStateNormal];
        NSURL *img_url=[NSURL URLWithString:childrenInfo.childThumbImage];
        [childProfile.view_childImage loadImageFromURL:img_url];
        [self.scroll_view addSubview:childProfile];
        [childProfile.btn_selectImage addTarget:self action:@selector(onClick_Photo:) forControlEvents:UIControlEventTouchUpInside];
       
    }
    if (array_childRecord.count<=3) {
        img_next.hidden = true;
    }
    else
        img_next.hidden = false;
    self.scroll_view.contentSize = CGSizeMake(j+10,112);
}
-(void)onClick_Photo:(id)sender
{
    UIButton *btn=(UIButton*)sender;
    btn.selected=!btn.selected;
    DDLogInfo(@"tag is %ld",(long)btn.tag);
    str_btnTag = [NSString stringWithFormat:@"%ld",(long)btn.tag];
    if (btn.selected)
    {
        if (array_childCount.count<4)
        {
            [array_childCount addObject:str_btnTag];
            [[ApplicationManager getInstance].array_selectedChild addObject:[array_childRecord objectAtIndex:btn.tag]];
            if (array_childCount.count==4) {
                btn_addChild.enabled = false;
            }
            else
                btn_addChild.enabled = true;
            
        }
        else
        {
            btn_addChild.enabled = false;
            btn.selected = !btn.selected;
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kChildLimit];
        }
    }
    else
    {
        btn_addChild.enabled = true;
        [array_childCount removeObject:str_btnTag];
        [[ApplicationManager getInstance].array_selectedChild removeObject:[array_childRecord objectAtIndex:btn.tag]];
        
    }
    DDLogInfo(@"Array count is %@",array_childCount);
    DDLogInfo(@"Selected child is %@",[ApplicationManager getInstance].array_selectedChild);
    
}
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section;
{
    return array_statelist.count;
}
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath;
{
    
    static NSString *simpleTableIdentifier = @"SimpleTableItem";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:simpleTableIdentifier];
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:simpleTableIdentifier];
    }
    [cell.textLabel setFont:[UIFont systemFontOfSize:14]];
    cell.textLabel.text = [[array_statelist objectAtIndex:indexPath.row]safeObjectForKey:kState];
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    txt_state.text = [[array_statelist objectAtIndex:indexPath.row]safeObjectForKey:kState];
    str_stateId = [[array_statelist objectAtIndex:indexPath.row]safeObjectForKey:kStateId];
    [UIView beginAnimations:@"view_state" context: nil];
    [UIView setAnimationBeginsFromCurrentState:YES];
    [UIView setAnimationDuration:0.25];
    [tbl_stateList setFrame:CGRectMake(btn_stateList.frame.origin.x, tbl_stateList.frame.origin.y,btn_stateList.frame.size.width,0)];
    [UIView commitAnimations ];
    checkDropState = 0;    
    [self.backgroundScrollView addGestureRecognizer:self.tapRecognizer];
    
    
}
#pragma mark - SMCoreNetworkManagerDelegate
- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                // [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kMessage]];
                [array_childData removeAllObjects];
                self.parentInfo.tokenData = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kTokenData];
                DDLogInfo(@"all Keys are %@",[[[dict_responseObj objectForKey:kData]objectForKey:kChildList] allKeys]);
                NSArray *tempArray=[[[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kChildList] allKeys];
                for (int i =0; i<[[[[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kChildList] allKeys]count]; i++) {
                    [array_childData addObject:[[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kChildList] safeObjectForKey:[tempArray safeObjectAtIndex:i]]];
                }
                [[ApplicationManager getInstance]saveChildData:array_childData];

                [self setDataInScrollView];
                consForchildmsg.constant=6;
                consForJobdetailView.constant=210;
            }
            else
            {
                if ([[dict_responseObj valueForKey:kErrorCode] isEqualToString:@"ES2"]) {
                    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                }
             
                consForchildmsg.constant=-120;
                consForJobdetailView.constant=85;
                img_next.hidden = true;
            }
            
            break;
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                
                array_statelist = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kStateList];
                [ApplicationManager getInstance].array_stateList = [array_statelist mutableCopy];
                DDLogInfo(@"State list is %@",array_statelist);
                for (int i=0; i<= array_statelist.count-1 ; i++) {
                    [array_states addObject:[[array_statelist objectAtIndex:i]safeObjectForKey:kState]];
                }
                DDLogInfo(@"states is %@",array_states);
                [tbl_stateList reloadData];
                
            }
            else
            {
                //  [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
        case 6:
            [self logout:dict_responseObj];
            break;
            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription]];
    
}
@end
