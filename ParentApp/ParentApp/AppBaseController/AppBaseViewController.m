//
//  AppBaseViewController.m
//  Boating App
//
//  Created by Tushar on 16/02/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import <SMCoreComponent/NSArray+Safe.h>
#import "MyProfileViewController.h"
#import "ChangePasswordViewController.h"


//#define kOFFSET_FOR_KEYBOARD 80.0
#define kNavBarHeight 0.0

#define IS_IPAD (UI_USER_INTERFACE_IDIOM() == UIUserInterfaceIdiomPad)
#define IS_IPHONE (UI_USER_INTERFACE_IDIOM() == UIUserInterfaceIdiomPhone)
#define IS_RETINA ([[UIScreen mainScreen] scale] >= 2.0)

#define SCREEN_WIDTH ([[UIScreen mainScreen] bounds].size.width)
#define SCREEN_HEIGHT ([[UIScreen mainScreen] bounds].size.height)
#define SCREEN_MAX_LENGTH (MAX(SCREEN_WIDTH, SCREEN_HEIGHT))
#define SCREEN_MIN_LENGTH (MIN(SCREEN_WIDTH, SCREEN_HEIGHT))

#define IS_IPHONE_4_OR_LESS (IS_IPHONE && SCREEN_MAX_LENGTH < 568.0)
#define IS_IPHONE_5 (IS_IPHONE && SCREEN_MAX_LENGTH == 568.0)
#define IS_IPHONE_6 (IS_IPHONE && SCREEN_MAX_LENGTH == 667.0)
#define IS_IPHONE_6P (IS_IPHONE && SCREEN_MAX_LENGTH == 736.0)


@interface AppBaseViewController (){
    AppDelegate *appDelegate;
}
@property (nonatomic, assign) BOOL keyboardIsShown;
@property (nonatomic) CGSize originalBgScrContentSize;
@property (nonatomic) CGRect originalBGScrRect;
@end

@implementation AppBaseViewController
@synthesize backgroundScrollView=_backgroundScrollView;
@synthesize keyboardIsShown=_keyboardIsShown;
@synthesize enableScrolling=_enableScrolling;
@synthesize activeTextView=_activeTextView;

#pragma mark - UIViewControllerDelegates

- (void)viewDidLoad {
    [super viewDidLoad];
    self.edgesForExtendedLayout = UIRectEdgeNone;
    self.navigationItem.backBarButtonItem =
    [[UIBarButtonItem alloc] initWithTitle:@""
                                      style:UIBarButtonItemStylePlain
                                     target:nil
                                     action:nil];
    kAddSettingButtonForNavigation;
    isSettingOpened=true;
    self.automaticallyAdjustsScrollViewInsets=NO;
    //self.enableScrolling = YES;
    // Do any additional setup after loading the view.
   // UIImage *image = [UIImage imageNamed: @"header-logo"];
   // UIImageView *imageview = [[UIImageView alloc] initWithImage: image];
  //  imageview.frame=CGRectMake(0, 0, 80, 21);
    // set the text view to the image view
  //  self.navigationItem.titleView = imageview;
//    self.navigationController.navigationBar.tintColor = [UIColor whiteColor];
//    self.navigationController.navigationBar.titleTextAttributes = @{NSForegroundColorAttributeName : [UIColor whiteColor],NSFontAttributeName : [UIFont fontWithName:RobotoLightfont size:17]};
    appDelegate=kAppDelegate;
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    loadingView=[[UIView alloc] initWithFrame:CGRectMake(0, 0, SCREEN_WIDTH, SCREEN_HEIGHT)];
    [loadingView setBackgroundColor:[UIColor blackColor]];
    [loadingView setAlpha:0.5];
    activityView = [[UIActivityIndicatorView alloc] initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
    activityView.hidesWhenStopped = YES;
    activityView.center = CGPointMake(appDelegate.window.frame.size.width / 2.0f, appDelegate.window.frame.size.height / 2.0f);
    activityView.autoresizingMask = UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleRightMargin | UIViewAutoresizingFlexibleBottomMargin;
//    activityView.color = [UIColor blackColor];
    [loadingView addSubview:activityView];
    [activityView stopAnimating];
    arryList=@[@"Profile",@"Change Password",@"Logout"];
    CGRect screenRect = [[UIScreen mainScreen] bounds];
    view_external=[[UIView alloc] initWithFrame:screenRect];
    _tapRecognizer = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(onTouchOnBackground:)];
    [self.backgroundScrollView addGestureRecognizer:_tapRecognizer];

}


- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    DDLogInfo(@"pre ----frame is %@",NSStringFromCGRect(loadingView.frame));

    // register for keyboard notifications
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardWillShow:)
                                                 name:UIKeyboardWillShowNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardWillHide:)
                                                 name:UIKeyboardWillHideNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(startNetworkActivity:) name:@"SHOW_ACTIVITY_INDICATOR" object:nil];
//     [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(startNetworkActivityForLogout:) name:@"SHOW_ACTIVITY_INDICATORFORLOGOUT:" object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(stopNetworkActivity) name:@"HIDE_ACTIVITY_INDICATOR" object:nil];
   

}

- (void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
      height = self.backgroundScrollView.contentSize.height;
//    DDLogInfo(@"screen width=%f",SCREEN_WIDTH);
    self.originalBgScrContentSize = CGSizeMake(SCREEN_WIDTH, height);
    self.originalBGScrRect = self.backgroundScrollView.frame;
    [self calculateScrollingofView];
}

- (void)viewWillDisappear:(BOOL)animated
{
    [super viewWillDisappear:animated];
//    // unregister for keyboard notifications while not visible.
//    [[NSNotificationCenter defaultCenter] removeObserver:self
//                                                    name:UIKeyboardWillShowNotification
//                                                  object:nil];
//    
//    [[NSNotificationCenter defaultCenter] removeObserver:self
//                                                    name:UIKeyboardWillHideNotification
//                                                  object:nil];
//    
//    [[NSNotificationCenter defaultCenter] removeObserver:self
//                                                    name:@"SHOW_ACTIVITY_INDICATOR"
//                                                  object:nil];
//    [[NSNotificationCenter defaultCenter] removeObserver:self
//                                                    name:@"HIDE_ACTIVITY_INDICATOR"
//                                                  object:nil];
//    [[NSNotificationCenter defaultCenter] removeObserver:self
//                                                    name:@"SHOW_ACTIVITY_INDICATORFORLOGOUT"
//                                                  object:nil];
    
   
    
    
}
-(void)dealloc
{
     [[NSNotificationCenter defaultCenter] removeObserver:self];
}
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(CGSize)GetHeightDyanamic:(UILabel*)lbl
{
    NSRange range = NSMakeRange(0, [lbl.text length]);
    CGSize constraint;
    constraint= CGSizeMake(288 ,MAXFLOAT);
    CGSize size;
    
    if (([[[UIDevice currentDevice] systemVersion] floatValue] >= 7.0)) {
        NSDictionary *attributes = [lbl.attributedText attributesAtIndex:0 effectiveRange:&range];
        CGSize boundingBox = [lbl.text boundingRectWithSize:constraint options: NSStringDrawingUsesLineFragmentOrigin attributes:attributes context:nil].size;
        
        size = CGSizeMake(ceil(boundingBox.width), ceil(boundingBox.height));
    }
    else{
        
        size = [lbl.text sizeWithFont:[UIFont fontWithName:@"Helvetica" size:14] constrainedToSize:constraint lineBreakMode:NSLineBreakByWordWrapping];
    }
    return size;
}


/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/

#pragma mark - UITextViewDelegate

- (BOOL)textViewShouldBeginEditing:(UITextView *)textView{
// [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,textView.frame.origin.y+216) animated:YES];
    
    return YES;
}

- (BOOL)textViewShouldEndEditing:(UITextView *)textView
{
//    [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,0) animated:YES];
//    [self.backgroundScrollView setContentSize:CGSizeMake(self.view.frame.size.width,1200)];

    return true;
}

#pragma mark - UITextFieldDelegate

-(void)textFieldDidBeginEditing:(UITextField *)sender
{
    
//    if (sender.tag==1)
//    {
//     [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,180) animated:YES];
//    }
    /*if ([sender isEqual:mailTf])
     {
     //move the main view, so that the keyboard does not hide it.
     if  (self.view.frame.origin.y >= 0)
     {
     [self setViewMovedUp:YES];
     }
     }*/
}

- (void)textFieldShouldReturn:(UITextField *)sender
{
    
}




//#pragma mark - API response
//- (void)requestDidSucceedWithResponseObject:(id)responseObject
//                                   withTask:(NSURLSessionDataTask *)task
//                              withRequestId:(NSUInteger)requestId{
//    [self stopNetworkActivity];
// }
//- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
//    [self stopNetworkActivity];
//    DDLogInfo(@"Request Failed with Error: %@, +++++++ %@", error, (NSError*)[error userInfo]);
//   [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
//}

#pragma mark- Delegate Methods for Dropdown
-(void)showPopUpWithTitle:(NSString*)popupTitle withOption:(NSArray*)arrOptions xy:(CGPoint)point size:(CGSize)size isMultiple:(BOOL)isMultiple{
    if (isSettingOpened==true)
    {
        Dropobj = [[DropDownListView alloc] initWithTitle:popupTitle options:arrOptions xy:point size:size isMultiple:isMultiple];
        Dropobj.delegate = self;
        [view_external setBackgroundColor:[UIColor clearColor]];
        [self.view addSubview:view_external];
        [Dropobj showInView:view_external animated:YES];
        
        /*----------------Set DropDown backGroundColor-----------------*/
        [Dropobj SetBackGroundDropDown_R:2.0 G:120.0 B:120.0 alpha:1.0];
        isSettingOpened=false;
    }
    else
    {
         [view_external removeFromSuperview];
        isSettingOpened=true;
        
    }
}
- (void)DropDownListView:(DropDownListView *)dropdownListView didSelectedIndex:(NSInteger)anIndex{
    /*----------------Get Selected Value[Single selection]-----------------*/
    //    _lblSelectedCountryNames.text=[arryList objectAtIndex:anIndex];
    isSettingOpened=true;
    if (anIndex == 0) {
        [self onClick_logOut];
    }
    if (anIndex == 1) {
        [self onClickMyProfile];
    }
    if (anIndex == 2) {
        [self onClickChangePassword];
    }
    [view_external removeFromSuperview];
    
}
- (void)DropDownListView:(DropDownListView *)dropdownListView Datalist:(NSMutableArray*)ArryData{
    
    /*----------------Get Selected Value[Multiple selection]-----------------*/
    //    if (ArryData.count>0) {
    //        _lblSelectedCountryNames.text=[ArryData componentsJoinedByString:@"\n"];
    //        CGSize size=[self GetHeightDyanamic:_lblSelectedCountryNames];
    //        _lblSelectedCountryNames.frame=CGRectMake(16, 240, 287, size.height);
    //    }
    //    else{
    //        _lblSelectedCountryNames.text=@"";
    //    }
    
}
- (void)DropDownListViewDidCancel{
    
}
- (void)touchesBegan:(NSSet *)touches withEvent:(UIEvent *)event {
    UITouch *touch = [touches anyObject];
    [view_external removeFromSuperview];
    if ([touch.view isKindOfClass:[UIView class]]) {
        [Dropobj fadeOut];
    }
    [self.view endEditing:YES];
    isSettingOpened=true;

}


#pragma mark - UserDefinedMethods

- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
     [self.view endEditing:YES];
}

/**
 This method is called when user clicks on logout
 @param nil
 @returns none
 */
-(void) onClick_logOut{
    
     UIAlertView *alert=[[UIAlertView alloc] initWithTitle:@"" message:kConfirmationMsgForLogout delegate:self cancelButtonTitle:@"Yes" otherButtonTitles:@"No", nil];
   
    [alert setTag:1002];
    [alert show];

}
-(void)onClickMyProfile
{
    int checkValue = 0;
    NSArray *arr = [self.navigationController viewControllers];
    for (int i=0;i<arr.count;i++) {
        if ([[arr objectAtIndex:i] isKindOfClass:[MyProfileViewController class]])
        {
            checkValue = i;
            break;
        }
        else
        {
            checkValue = 0;
        }
    }
    
    if (checkValue == 0) {
        MyProfileViewController *myProfile = [[MyProfileViewController alloc]initWithNibName:@"MyProfileViewController" bundle:nil];
        [self.navigationController pushViewController:myProfile animated:YES];
    }
    else
    [self.navigationController popToViewController:[arr objectAtIndex:checkValue] animated:YES];
}
-(void)onClickChangePassword
{
    ChangePasswordViewController *changePassword = [[ChangePasswordViewController alloc]initWithNibName:@"ChangePasswordViewController" bundle:nil];
    [self.navigationController pushViewController:changePassword animated:YES];
}
/**
 This method is celled when user select any menu from the menu list
 @param id send
 @returns none
 */
-(void)onClickedMenu:(id)sender{
    if (isSettingOpened)
    {
        [self.menuPopover dismissMenuPopover];
        self.menuPopover = [[MLKMenuPopover alloc] initWithFrame:MENU_POPOVER_FRAME menuItems:arryList];
        self.menuPopover.menuPopoverDelegate = self;
        [self.menuPopover showInView:self.view];
        isSettingOpened=false;
    }
    else
    {
        [self.menuPopover dismissMenuPopover];
        isSettingOpened=true;
    }

}
-(void)addLogoutButtonForNavigationBar{
    UIBarButtonItem *barBtn_logout = [[UIBarButtonItem alloc]
                                    initWithTitle:@"Logout"
                                    style:UIBarButtonItemStyleBordered
                                    target:self
                                    action:@selector(onClick_logOut)];
    self.navigationItem.leftBarButtonItem = barBtn_logout;
}
#pragma mark MLKMenuPopoverDelegate

- (void)menuPopover:(MLKMenuPopover *)menuPopover didSelectMenuItemAtIndex:(NSInteger)selectedIndex
{
    [self.menuPopover dismissMenuPopover];
    switch (selectedIndex)
    {
        case 0://User profile
            [self onClickMyProfile];
            break;
        case 1:// Changes password
            [self onClickChangePassword];
            break;
        case 2://Logout
             [self onClick_logOut];
            break;
        default:
            break;
    }
    
}
//-(void)setNotificationSettingForApp:(id)sender{
//    UISwitch *sw=(UISwitch*)sender;
//    [self startNetworkActivity:NO];
//    NSString *strNotify=[NSString stringWithFormat:@"%@",sw.isOn==0?@"No":@"Yes"];
//    [[ApplicationManager getInstance] updateAppNotificationSeting:strNotify];
//    
//}
-(void)updateMenuState:(id)sender{
    isSettingOpened=!isSettingOpened;
}
- (void)startNetworkActivity:(BOOL)shouldShowOnView{

    DDLogInfo(@"pre ----center is %@",NSStringFromCGPoint(activityView.center));
    DDLogInfo(@"pre ----frame is %@",NSStringFromCGRect(loadingView.frame));

        if(shouldShowOnView)
        {
            loadingView.frame = CGRectMake(0, 0, SCREEN_WIDTH, SCREEN_HEIGHT-64);
            [self.view addSubview:loadingView];
            
        }
        else{
            [loadingView setFrame:appDelegate.window.frame];
           
            [appDelegate.window addSubview:loadingView];
        }
     activityView.center = CGPointMake(loadingView.frame.size.width / 2.0f, loadingView.frame.size.height / 2.0f);
    DDLogInfo(@"center is %@",NSStringFromCGPoint(activityView.center));
    DDLogInfo(@"frame is %@",NSStringFromCGRect(loadingView.frame));
        [activityView setHidden:NO];
        [activityView startAnimating];
 
}


- (void)stopNetworkActivity{
   // [loadingView setHidden:YES];
    [activityView stopAnimating];
    [loadingView removeFromSuperview];
}

- (void)calculateScrollingofView{
    if (self.backgroundScrollView.frame.size.height<self.backgroundScrollView.contentSize.height) {
        self.enableScrolling = YES;
        self.backgroundScrollView.scrollEnabled = self.enableScrolling;
    }else{
        self.enableScrolling = NO;
        self.backgroundScrollView.scrollEnabled = self.enableScrolling;
    }
}
- (void)keyboardWillHide:(NSNotification *)n
{
    NSDictionary* userInfo = [n userInfo];
    [self setScrollViewMoveUp:NO withKeyboardSize:[[userInfo objectForKey:UIKeyboardFrameBeginUserInfoKey] CGRectValue]];
    _keyboardIsShown = NO;
}

- (void)keyboardWillShow:(NSNotification *)n
{
    if (_keyboardIsShown) {
        return;
    }
    NSDictionary* userInfo = [n userInfo];
    CGSize keyboardSize = [[userInfo objectForKey:UIKeyboardFrameBeginUserInfoKey] CGRectValue].size;
    CGRect visibleRect = self.view.frame;
    visibleRect.size.height -= keyboardSize.height;
    self.defaultContentSize=CGSizeMake(self.backgroundScrollView.contentSize.width, self.backgroundScrollView.contentSize.height) ;
    [self setScrollViewMoveUp:YES withKeyboardSize:[[userInfo objectForKey:UIKeyboardFrameBeginUserInfoKey] CGRectValue]];
    // resize the noteView
    _keyboardIsShown = YES;
}

- (UIView *)activeTextViewFromSubviews:(NSArray *)subviews {
    for (int i = 0;i<[subviews count];i++) {
        UIView *view = [subviews safeObjectAtIndex:i];
        if (view.subviews.count) {
            UIView *activeView = [self activeTextViewFromSubviews:view.subviews];
            if (activeView.isFirstResponder) {
                return activeView;
            }
        }
        else {
            if (view.isFirstResponder) {
                return view;
            }
        }
    }
    return nil;
}

- (void)setScrollViewMoveUp:(BOOL)moveUP withKeyboardSize:(CGRect)keyboardFrame{
    CGRect kbframe = keyboardFrame;
    CGRect viewFrame = CGRectMake(self.backgroundScrollView.frame.origin.x, self.backgroundScrollView.frame.origin.y, SCREEN_WIDTH, self.backgroundScrollView.contentSize.height);
    CGRect keyboardIntersectionFrame = CGRectIntersection(self.backgroundScrollView.frame, kbframe);
    if (moveUP) {
        UIEdgeInsets contentInsets = UIEdgeInsetsMake(0.0, 0.0, kbframe.size.height, 0.0);
        self.backgroundScrollView.contentInset = contentInsets;
        self.backgroundScrollView.scrollIndicatorInsets = contentInsets;
        viewFrame.size.height += (kbframe.size.height - self.navigationController.navigationBar.frame.size.height);
        [self.backgroundScrollView setContentSize:viewFrame.size];
        
        
        self.backgroundScrollView.scrollEnabled = YES;
        self.activeTextView = [self activeTextViewFromSubviews:self.backgroundScrollView.subviews];
        CGRect aRect = self.backgroundScrollView.frame;
        aRect.size.height -= keyboardIntersectionFrame.size.height;
        if (!CGRectContainsPoint(aRect, self.activeTextView.frame.origin)) {
            [self.backgroundScrollView scrollRectToVisible:self.activeTextView.frame animated:YES];
        }
    }else{
        UIEdgeInsets contentInsets = UIEdgeInsetsMake(0.0, 0.0,0.0, 0.0);
        self.backgroundScrollView.contentInset = contentInsets;
        self.backgroundScrollView.scrollIndicatorInsets = contentInsets;
        [self.backgroundScrollView scrollRectToVisible:self.activeTextView.frame animated:YES];
        if ((SCREEN_HEIGHT==(viewFrame.size.height - (kbframe.size.height - self.navigationController.navigationBar.frame.size.height)))||(height+kbframe.size.height<=viewFrame.size.height)) {
            viewFrame.size.height -= (kbframe.size.height - self.navigationController.navigationBar.frame.size.height);
        }
        self.backgroundScrollView.scrollEnabled = self.enableScrolling;
        // self.backgroundScrollView.frame = CGRectMake(self.backgroundScrollView.frame.origin.x, self.backgroundScrollView.frame.origin.y, SCREEN_WIDTH, SCREEN_HEIGHT-self.backgroundScrollView.frame.origin.y);
        self.backgroundScrollView.contentSize = self.defaultContentSize;//CGSizeMake(SCREEN_WIDTH, height);
    }
    [self calculateScrollingofView];
    [UIView beginAnimations:nil context:NULL];
    [UIView setAnimationBeginsFromCurrentState:YES];
    [UIView commitAnimations];
}

//-------------------------------------------------------------------------------
//method is used for changing the format of date:
//-------------------------------------------------------------------------------
-(NSString *)convertDateFormate:(NSString *)str_date andDateFormate:(NSString *)dateFormate
{
    return str_date;
    NSString *dateStr =str_date;
    NSDateFormatter *dateFormatter1 = [[NSDateFormatter alloc] init];
    [dateFormatter1 setDateFormat:dateFormate];
    NSDate *date = [dateFormatter1 dateFromString:dateStr];
    NSTimeZone *currentTimeZone = [NSTimeZone localTimeZone];
    NSTimeZone *edtTimeZone = [NSTimeZone timeZoneWithAbbreviation:self.parentInfo.timeZone];
    NSInteger currentGMTOffset = [currentTimeZone secondsFromGMTForDate:date];
    NSInteger gmtOffset = [edtTimeZone secondsFromGMTForDate:date];
    NSTimeInterval gmtInterval = currentGMTOffset - gmtOffset;
    NSDate *destinationDate = [[NSDate alloc] initWithTimeInterval:gmtInterval sinceDate:date];
    // return destinationDate;
    NSDateFormatter *dateFormatters = [[NSDateFormatter alloc] init];
    [dateFormatters setDateFormat:@"hh:mm"];
    [dateFormatters setTimeZone:[NSTimeZone localTimeZone]];
    dateStr = [dateFormatter1 stringFromDate: destinationDate];
    //return dateStr;
    
}
#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 6:
            [UIApplication sharedApplication].applicationIconBadgeNumber = 0;
            [self logout:dict_responseObj];
            break;
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}

- (void)logout:(NSDictionary *)dict_responseObj{
    if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kMessage]];
        NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
        [userDefaults setObject:nil forKey:kDictKeyNSUser];
        [self.navigationController popToRootViewControllerAnimated:YES];
        
    }
    else
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
    }
}
-(void)logoutAlert:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    
    if ((alertView.tag == 1002) && (buttonIndex == 0)) {
        [self startNetworkActivity:NO];
        NSMutableDictionary *dict_logoutData = [[NSMutableDictionary alloc]init];
        
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        //  NSString *tokenData = [defaults objectForKey:kTokenValue];
        //  NSString *userId = [defaults objectForKey:@"defaultUserId"];
        NSString *userId = self.parentInfo.parentUserId;
        [defaults synchronize];
        [dict_logoutData setSafeObject:self.parentInfo.tokenData forKey:@"token"];
        
        [dict_logoutData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_logoutData setSafeObject:userId forKey:kUserId];
        
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kLogOutAPI];
        DDLogInfo(@"%@",kLogOutAPI);
        networkManager.delegate = self;
        [networkManager logOut:dict_logoutData forRequestNumber:6];
    }

    
}
- (void)showAlertForSelf:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message {
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:message delegate:vc cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alert setTag:1001];
    [alert show];
}
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    if (alertView.tag == 1001) {
        [self.navigationController popViewControllerAnimated:YES];
    }
    [self logoutAlert:alertView clickedButtonAtIndex:buttonIndex];
}
@end
