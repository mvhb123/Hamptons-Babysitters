//
//  AppBaseViewController.h
//  Boating App
//
//  Created by Tushar on 16/02/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AppDelegate.h"
#import "NIDropDown.h"
#import "DropDownListView.h"
#import "SMCoreNetworkFramework.h"
#import "Parent.h"
#import "MLKMenuPopover.h"
#import "ValidationManager.h"


@class TableViewWithBlock;
@interface AppBaseViewController : UIViewController<SMCoreNetworkManagerDelegate,UITableViewDelegate, UITableViewDataSource, kDropDownListViewDelegate,MLKMenuPopoverDelegate>{
    IBOutlet UIScrollView *backgroundScrollView;
    UIActivityIndicatorView *activityView;
    UIView *loadingView;
    NSString *animationDirection;
    BOOL isSearchDropDownOpened;
    NSMutableArray *array_menuOptions;
    float height;
    NSArray *arryList;
    DropDownListView * Dropobj;
    UIView *view_external;
    BOOL isSettingOpened;
    
}
@property(nonatomic,assign)Parent *parentInfo;
@property

(nonatomic, strong) IBOutlet UIScrollView *backgroundScrollView;
@property (nonatomic,strong)IBOutlet UIView *backgroundView;
@property (nonatomic, assign) BOOL enableScrolling;
@property (nonatomic, weak) UIView *activeTextView;
@property (nonatomic,strong) NIDropDown *dropDown;
@property (retain, nonatomic) IBOutlet TableViewWithBlock *tbl_Menu;
@property(nonatomic,retain)UITapGestureRecognizer *tapRecognizer;
@property(nonatomic)CGSize defaultContentSize;
@property(nonatomic,strong) MLKMenuPopover *menuPopover;

@property (retain) AppDelegate *appDelegate;
- (void)startNetworkActivity:(BOOL)shouldShowOnView;
- (void)stopNetworkActivity;
-(NSString *)convertDateFormate:(NSString *)str_date andDateFormate:(NSString *)dateFormate;
-(void)onClickedMenu:(id)sender;
- (void)calculateScrollingofView;
- (void)logout:(NSDictionary *)dict_responseObj;
-(void)addLogoutButtonForNavigationBar;
- (void)showAlertForSelf:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message;
//- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex;
-(void)logoutAlert:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex;
@end
