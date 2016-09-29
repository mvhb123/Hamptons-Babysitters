//
//  AppBaseViewController.m
//  Boating App
//
//  Created by Tushar on 16/02/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
//#import <SMCoreComponent/NSArray+Safe.h>
#import "ProfileViewController.h"
#import "ChangePasswordViewController.h"
#import "ContactAdminViewController.h"
#import "OurExpectationsViewController.h"

#define kOFFSET_FOR_KEYBOARD 80.0
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
#define MENU_POPOVER_FRAME  CGRectMake(self.view.frame.size.width-215, 0,210, 210)


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
    self.edgesForExtendedLayout=UIRectEdgeNone;
    self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:kNumberFormat];
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    kAddSettingButtonForNavigation;
    isSettingOpened=true;
    self.automaticallyAdjustsScrollViewInsets=NO;
    //self.enableScrolling = YES;
    // Do any additional setup after loading the view.
   // UIImage *image = [UIImage imageNamed: @"header-logo"];
   // UIImageView *imageview = [[UIImageView alloc] initWithImage: image];
   // imageview.frame=CGRectMake(0, 0, 80, 21);
    // set the text view to the image view
    //self.navigationItem.titleView = imageview;
    appDelegate=kAppDelegate;

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
    arryList=@[@"Profile",@"Notification all jobs",@"Change Password",@"Contact Admin",@"Our Expectations",@"Logout"];
    CGRect screenRect = [[UIScreen mainScreen] bounds];
    view_external=[[UIView alloc] initWithFrame:screenRect];
    _tapRecognizer = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(onTouchOnBackground:)];
    [self.backgroundScrollView addGestureRecognizer:_tapRecognizer];
     [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(startNetworkActivity:) name:@"SHOW_ACTIVITY_INDICATOR" object:nil];
     [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(stopNetworkActivity) name:@"HIDE_ACTIVITY_INDICATOR" object:nil];
    
}


- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    // register for keyboard notifications
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardWillShow:)
                                                 name:UIKeyboardWillShowNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardWillHide:)
                                                 name:UIKeyboardWillHideNotification
                                               object:nil];
    
   
//     [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(startNetworkActivityForLogout:) name:@"SHOW_ACTIVITY_INDICATORFORLOGOUT:" object:nil];
  
   

}

- (void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    height = self.backgroundScrollView.contentSize.height;
    self.originalBgScrContentSize = CGSizeMake(SCREEN_WIDTH, height);
    self.originalBGScrRect = self.backgroundScrollView.frame;
    [self calculateScrollingofView];
}

- (void)viewWillDisappear:(BOOL)animated
{
    [super viewWillDisappear:animated];
 
}

-(void)dealloc{
    [[NSNotificationCenter defaultCenter]removeObserver:self];
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
 //[self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x,160) animated:YES];
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
#pragma mark - API response
- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
   
 }
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    DDLogInfo(@"Request Failed with Error: %@, +++++++ %@", error, (NSError*)[error userInfo]);
   [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
}


#pragma mark MLKMenuPopoverDelegate

- (void)menuPopover:(MLKMenuPopover *)menuPopover didSelectMenuItemAtIndex:(NSInteger)selectedIndex
{
    [self.menuPopover dismissMenuPopover];
    switch (selectedIndex)
    {
        case 0://User profile
            [self onClick_Profile];
            break;
        case 1://App notification setting
            break;
        case 2:// Changes password
            [self onClick_ChangePassword];
            break;
        case 3:// Contact Admin
            [self onClick_contactAdmin];
            break;
        case 4://  Our Expectations
            [self onClick_ourExpectation];
            break;
        case 5:// logout
            [self onClick_logOut];
            break;
            
        default:
            break;
    }

}
-(void)setNotificationSettingForApp:(id)sender{
    UISwitch *sw=(UISwitch*)sender;
    [self startNetworkActivity:NO];
    NSString *strNotify=[NSString stringWithFormat:@"%@",sw.isOn==0?@"No":@"Yes"];
    [[ApplicationManager getInstance] updateAppNotificationSeting:strNotify];

}
-(void)updateMenuState:(id)sender{
    isSettingOpened=!isSettingOpened;
}
#pragma mark - UserDefinedMethods

- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {

}


-(void)onClick_Profile
{
    NSArray *array_Nav = [self.navigationController viewControllers];
    if(![[array_Nav objectAtIndex:[array_Nav count]-1] isKindOfClass:[ProfileViewController class]])
    {
       
            if ([[array_Nav objectAtIndex:0] isKindOfClass:[ProfileViewController class]])
            {
                [self.navigationController popToViewController:[array_Nav objectAtIndex:0] animated:YES];
            }
            else
            {
                ProfileViewController *profileViewController=[[ProfileViewController alloc] initWithNibName:@"ProfileViewController" bundle:nil];
                [self.navigationItem setTitle:@""];
                [self.navigationController pushViewController:profileViewController animated:YES];
            }
    }
    
}

/**
 This method is celled when user clicks on logout
 @param nil
 @returns none
 */
-(void) onClick_logOut{
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:kConfirmationMsgForLogout delegate:self cancelButtonTitle:@"Yes" otherButtonTitles:@"No",nil];
    [alert setTag:9999];
    [alert show];
   
   
}

-(void)onClick_ChangePassword
{
    NSArray *array_Nav = [self.navigationController viewControllers];
    if(![[array_Nav objectAtIndex:[array_Nav count]-1] isKindOfClass:[ChangePasswordViewController class]])
    {
            if ([[array_Nav objectAtIndex:0] isKindOfClass:[ChangePasswordViewController class]])
            {
                [self.navigationController popToViewController:[array_Nav objectAtIndex:0] animated:YES];

            }
            else
            {
                ChangePasswordViewController *changePassword=[[ChangePasswordViewController alloc] initWithNibName:@"ChangePasswordViewController" bundle:nil];
                [self.navigationItem setTitle:@""];
                [self.navigationController pushViewController:changePassword animated:YES];
            }
    }
 
}
-(void)onClick_contactAdmin
{
    NSArray *array_Nav = [self.navigationController viewControllers];
    if(![[array_Nav objectAtIndex:[array_Nav count]-1] isKindOfClass:[ContactAdminViewController class]])
    {
        if ([[array_Nav objectAtIndex:0] isKindOfClass:[ContactAdminViewController class]])
        {
            [self.navigationController popToViewController:[array_Nav objectAtIndex:0] animated:YES];
        }
        else
        {
            ContactAdminViewController *contactAdmin=[[ContactAdminViewController alloc] initWithNibName:@"ContactAdminViewController" bundle:nil];
            [self.navigationItem setTitle:@""];
            [self.navigationController pushViewController:contactAdmin animated:YES];
        }
    }
    
}
-(void)onClick_ourExpectation
{
    NSArray *array_Nav = [self.navigationController viewControllers];
    if(![[array_Nav objectAtIndex:[array_Nav count]-1] isKindOfClass:[OurExpectationsViewController class]])
    {
        if ([[array_Nav objectAtIndex:0] isKindOfClass:[OurExpectationsViewController class]])
        {
            [self.navigationController popToViewController:[array_Nav objectAtIndex:0] animated:YES];
        }
        else
        {
            OurExpectationsViewController *ourExp=[[OurExpectationsViewController alloc] initWithNibName:@"OurExpectationsViewController" bundle:nil];
            [self.navigationItem setTitle:@""];
            [self.navigationController pushViewController:ourExp animated:YES];
        }
    }
    
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

- (void)startNetworkActivity:(BOOL)shouldShowOnView{
    
    [activityView setHidden:NO];
    [loadingView setHidden:NO];
    if(shouldShowOnView){
            [loadingView setFrame:CGRectMake(0, 0, SCREEN_WIDTH, SCREEN_HEIGHT-64)];
            
            [self.view addSubview:loadingView];
            
        }else{
            [loadingView setFrame:appDelegate.window.frame];
            [appDelegate.window addSubview:loadingView];
        }
        activityView.center = CGPointMake(loadingView.frame.size.width / 2.0f, loadingView.frame.size.height / 2.0f);
        [activityView startAnimating];
}

- (void)appDidLogout:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex{
    if ((alertView.tag==9999)&&(buttonIndex==0)) {
        [self startNetworkActivity:NO];
        [[ApplicationManager getInstance] logOutAPI:nil];
    }
}

#pragma mark -Alert view delegates
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex{
    [self appDidLogout:alertView clickedButtonAtIndex:buttonIndex];
}

- (void)stopNetworkActivity{
    [activityView setHidden:YES];
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
    [self setScrollViewMoveUp:NO withKeyboardSize:[[userInfo objectForKey:UIKeyboardFrameEndUserInfoKey] CGRectValue]];
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
    DDLogInfo(@"------ content size %@",NSStringFromCGSize(self.defaultContentSize));
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
        self.activeTextView = [self activeTextViewFromSubviews:self.backgroundScrollView.subviews ];
        CGRect aRect = self.backgroundScrollView.frame;
        aRect.size.height -= keyboardIntersectionFrame.size.height;
        if (!CGRectContainsPoint(aRect, self.activeTextView.frame.origin)) {
            [self.backgroundScrollView scrollRectToVisible:self.activeTextView.frame animated:YES];
        }
    }else{
        UIEdgeInsets contentInsets = UIEdgeInsetsMake(0.0, 0.0, 0.0, 0.0);
        self.backgroundScrollView.contentInset = contentInsets;
        self.backgroundScrollView.scrollIndicatorInsets = contentInsets;
        [self.backgroundScrollView scrollRectToVisible:self.activeTextView.frame animated:YES];
        if ((SCREEN_HEIGHT==(viewFrame.size.height - (kbframe.size.height - self.navigationController.navigationBar.frame.size.height)))||(height+kbframe.size.height<=viewFrame.size.height)) {
            viewFrame.size.height -= (kbframe.size.height - self.navigationController.navigationBar.frame.size.height);
        }
        self.backgroundScrollView.scrollEnabled = self.enableScrolling;
      //  self.backgroundScrollView.frame = CGRectMake(self.backgroundScrollView.frame.origin.x, self.backgroundScrollView.frame.origin.y, SCREEN_WIDTH, SCREEN_HEIGHT-self.backgroundScrollView.frame.origin.y);
        self.backgroundScrollView.contentSize = self.defaultContentSize;//CGSizeMake(SCREEN_WIDTH, height);
        
    }    
    [self calculateScrollingofView];
    [UIView beginAnimations:nil context:NULL];
    [UIView setAnimationBeginsFromCurrentState:YES];
    [UIView commitAnimations];
}


@end
