//
//  AppBaseViewController.h
//  Boating App
//
//  Created by Tushar on 16/02/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "NIDropDown.h"
#import "SMCoreNetworkFramework.h"
#import "MLKMenuPopover.h"
#import "NumberFormatter.h"
@class TableViewWithBlock;
@interface AppBaseViewController : UIViewController<SMCoreNetworkManagerDelegate,UITableViewDelegate, UITableViewDataSource,MLKMenuPopoverDelegate>{
    IBOutlet UIScrollView *backgroundScrollView;
    UIActivityIndicatorView *activityView;
    UIView *loadingView;
    NSString *animationDirection;
    BOOL isSearchDropDownOpened;
    NSMutableArray *array_menuOptions;
    float height;
    NSArray *arryList;
    UIView *view_external;
    BOOL isSettingOpened;
}
@property (nonatomic ,strong)NumberFormatter *numFormatter;
@property (nonatomic, strong) IBOutlet UIScrollView *backgroundScrollView;
@property (nonatomic, assign) BOOL enableScrolling;
@property (nonatomic, weak) UIView *activeTextView;
@property (nonatomic,strong) NIDropDown *dropDown;
@property (retain, nonatomic) IBOutlet TableViewWithBlock *tbl_Menu;
@property(nonatomic,retain)UITapGestureRecognizer *tapRecognizer;
@property (nonatomic,retain)UIBarButtonItem *btnMenu;
@property(nonatomic,strong) MLKMenuPopover *menuPopover;
@property(nonatomic,weak)Sitter *sitterInfo;
@property(nonatomic)CGSize defaultContentSize;
- (void)startNetworkActivity:(BOOL)shouldShowOnView;
- (void)stopNetworkActivity;
- (void)appDidLogout:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex;
-(NSString *)convertDateFormate:(NSString *)str_date andDateFormate:(NSString *)dateFormate;
@end
